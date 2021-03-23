#!/bin/bash
set -e

export WPCLI="php $WPCLI_PATH --allow-root --path=$WP_FOLDER"
export WP_FOLDER="/usr/src/wordpress"
export WPCLI="php /home/util/wp-cli.phar --allow-root --path=$WP_FOLDER"

python3 "$WP_FOLDER/wait_for_mysql.py"

if ! $($WPCLI core is-installed) ; then
  echo "== INSTALLING WORDPRESS =="
  $WPCLI_COMMAND core install --url="0.0.0.0:8000" --title="$WORDPRESS_TITLE" --admin_user="$WORDPRESS_ADMIN_USER" --admin_password="$WORDPRESS_ADMIN_PASSWORD" --admin_email="$WORDPRESS_ADMIN_EMAIL"

  echo "== ACTIVATING PLUGINS =="
  $WPCLI plugin activate helmholtz-plugin

  echo "== ACTIVATING THEMES =="
  $WPCLI theme activate helmholtz-theme
fi


# https://github.com/docker-library/wordpress/blob/master/latest/php7.4/apache/docker-entrypoint.sh
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

echo "== STARTING APACHE SERVER =="
apache2-foreground