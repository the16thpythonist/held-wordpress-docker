#!/bin/bash
# This script will be executed whenever the container is started. It consists of the following basic steps:
# - Wait until the database connection can be established
# - Check if wordpress is already installed to the database. If not, install wordpress into the database
# - Execute the entrypoint script, which copies the wp data from folder /usr/src/wordpress to /var/www/html
# - Run the apache web server in the foreground
set -e

export PATH="$PATH:/usr/local/mysql/bin"
export WP_FOLDER="/usr/src/wordpress"
export WP_INSTALLED="$WP_FOLDER/.installed"
export WPCLI_COMMAND="php /home/util/wp-cli.phar --allow-root --path=$WP_FOLDER"

# This python script waits for the database connection to be established, by continuously attempting to open a new
# database connector for the mysql socket and retrying in case the attempt fails. Usually this kind of operation would
# not be necessary for OpenShift. But it is necessary for local testing of the container. Local testing is realized
# with docker-compose. Both the web and db container are started at the same time and thus one cannot be sure that the
# db is already running when attempting to access it. So better be safe and wait for it in the case that the db is
# taking longer than expected to be available...
python3 "$WP_FOLDER/wait_for_mysql.py"


# wpcli also has a command "is-installed", which should ideally return if the wordpress instance is already installed
# by actually checking the database. I tried using that, but it did not work out. So instead we just assume here that
# the installation only has to be performed once. We use a simple trick where we check for the existence of a certain
# file, which we use as an indicator flag for the installation, creating it after we performed the installation.
# (NOTE: Even if the installation commands do get executed when an installation already exists, it is not a problem
# since they will just fail without an error)
echo "== TESTING FOR WORDPRESS INSTALLATION =="
if ! test -f $WP_INSTALLED ; then

  # Usually when creating a new wordpress instance, you would have to install it when first opening the site. It would
  # prompt the user to create an admin account etc. This command does that automatically, using the previously defined
  # environmental variables.
  echo "== INSTALLING WORDPRESS =="
  $WPCLI_COMMAND core install \
                 --url="$WORDPRESS_DOMAIN" \
                 --title="$WORDPRESS_TITLE" \
                 --admin_user="$WORDPRESS_ADMIN_USER" \
                 --admin_password="$WORDPRESS_ADMIN_PASSWORD" \
                 --admin_email="$WORDPRESS_ADMIN_EMAIL" \

  echo "== MIGRATING THE BACKUP =="
  # Unzipping the media content: All the images which have been uploaded to the old website
  unzip "$WP_FOLDER/uploads.zip" -d "$WP_FOLDER/wp-content"

  # This will use the exported SQL file of the backup to recreate this structure within the new database.
  # "core update-db" updates the database for a new wp version. That is only important for the case that the SQL
  # file comes from a very old version of WP (which is the case here)
  # NOTE: These commands might be the only ones maybe hurting a little bit, when this section is executed with an
  # already existing wordpress installation. The whole current db would be overwritten with the state of the backup.
  $WPCLI_COMMAND --quiet db import  "$WP_FOLDER/wordpress.sql"
  $WPCLI_COMMAND core update-db

  # A workaround for this specific backup. The database has the domain name hardcoded into some of it's entries. By
  # migrating to a new server, one most likely does not have the same domain name. The "search-replace" command can
  # be used to replace the old domain with the new one.
  # TODO: This could be optimized to NOT be hardcoded anymore!
  export OLD_DOMAIN="localhost/tmp/ufo"
  $WPCLI_COMMAND search-replace "http://$OLD_DOMAIN" "http://$WORDPRESS_DOMAIN"

  echo "== ACTIVATING PLUGINS =="
  $WPCLI_COMMAND plugin activate venture-lite-companion widget-logic svg-support duplicator helmholtz-plugin
  $WPCLI_COMMAND plugin update --all

  echo "== ACTIVATING THEMES =="
  $WPCLI_COMMAND theme activate helmholtz-theme

  echo "true" > $WP_INSTALLED
  test -f $WP_INSTALLED
else
  echo "Wordpress is already installed. Skipping ..."
fi


# --- START ENTRYPOINT SCRIPT ---
# The following code has been copied from this file:
# https://github.com/docker-library/wordpress/blob/master/latest/php7.4/apache/docker-entrypoint.sh
# It is a script, which would usually be used as the ENTRYPOINT for the docker container, but since we are using the
# shell variant of the CMD command and not the args variant, the entrypoint does not get used. Apparently it contains
# something important though because without it, this didn't work. It did work when I just copied the content here.
# My understanding is, that it mainly copies the source code from /usr/src/wordpress into /var/www/html to actually be
# served.
echo "== EXECUTE entrypoint.sh =="
if [ ! -e index.php ] && [ ! -e wp-includes/version.php ]; then
  # if the directory exists and WordPress doesn't appear to be installed AND the permissions of it are root:root, let's chown it (likely a Docker-created directory)
  if [ "$uid" = '0' ] && [ "$(stat -c '%u:%g' .)" = '0:0' ]; then
    chown "$user:$group" .
  fi

  echo >&2 "WordPress not found in $PWD - copying now..."
  if [ -n "$(find -mindepth 1 -maxdepth 1 -not -name wp-content)" ]; then
    echo >&2 "WARNING: $PWD is not empty! (copying anyhow)"
  fi
  sourceTarArgs=(
    --create
    --file -
    --directory /usr/src/wordpress
    --owner "$user" --group "$group"
  )
  targetTarArgs=(
    --extract
    --file -
  )
  if [ "$uid" != '0' ]; then
    # avoid "tar: .: Cannot utime: Operation not permitted" and "tar: .: Cannot change mode to rwxr-xr-x: Operation not permitted"
    targetTarArgs+=( --no-overwrite-dir )
  fi
  # loop over "pluggable" content in the source, and if it already exists in the destination, skip it
  # https://github.com/docker-library/wordpress/issues/506 ("wp-content" persisted, "akismet" updated, WordPress container restarted/recreated, "akismet" downgraded)
  for contentDir in /usr/src/wordpress/wp-content/*/*/; do
    contentDir="${contentDir%/}"
    [ -d "$contentDir" ] || continue
    contentPath="${contentDir#/usr/src/wordpress/}" # "wp-content/plugins/akismet", etc.
    if [ -d "$PWD/$contentPath" ]; then
      echo >&2 "WARNING: '$PWD/$contentPath' exists! (not copying the WordPress version)"
      sourceTarArgs+=( --exclude "./$contentPath" )
    fi
  done
  tar "${sourceTarArgs[@]}" . | tar "${targetTarArgs[@]}"
  echo >&2 "Complete! WordPress has been successfully copied to $PWD"
fi

wpEnvs=( "${!WORDPRESS_@}" )
if [ ! -s wp-config.php ] && [ "${#wpEnvs[@]}" -gt 0 ]; then
  for wpConfigDocker in \
    wp-config-docker.php \
    /usr/src/wordpress/wp-config-docker.php \
  ; do
    if [ -s "$wpConfigDocker" ]; then
      echo >&2 "No 'wp-config.php' found in $PWD, but 'WORDPRESS_...' variables supplied; copying '$wpConfigDocker' (${wpEnvs[*]})"
      # using "awk" to replace all instances of "put your unique phrase here" with a properly unique string (for AUTH_KEY and friends to have safe defaults if they aren't specified with environment variables)
      awk '
        /put your unique phrase here/ {
          cmd = "head -c1m /dev/urandom | sha1sum | cut -d\\  -f1"
          cmd | getline str
          close(cmd)
          gsub("put your unique phrase here", str)
        }
        { print }
      ' "$wpConfigDocker" > wp-config.php
      if [ "$uid" = '0' ]; then
        # attempt to ensure that wp-config.php is owned by the run user
        # could be on a filesystem that doesn't allow chown (like some NFS setups)
        chown "$user:$group" wp-config.php || true
      fi
      break
    fi
  done
fi
# --- STOP ENTRYPOINT SCRIPT ---


echo "== STARTING APACHE SERVER =="
# "apache2-foreground" is a custom executable script, which comes shipped with the base container wordpress:latest.
# It starts the actual apache web server. As far as I understand it also does some other stuff with permissions and
# setting env variables. So one probably cannot just start apache the "normal" way.
apache2-foreground