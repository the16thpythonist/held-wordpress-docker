FROM wordpress:latest

MAINTAINER Jonas Teufel <jonseb1998@gmail.com>


# === (1) INSTALLING SYSTEM DEPENDENCIES ===
# mysql-signature contains a public key, which is needed to authenticate the authenticity of an additional PPA
# repository, namely that for the MySQL foundation.
# This following section only deals with the necessary setup required to make the mysql PPA repo work so that we
# can later install the system package "mysql-community-client".
COPY ./mysql-signature /tmp/mysql-signature
RUN apt-get update && \
    apt-get install -y gnupg && \
    apt-key add /tmp/mysql-signature && \
    echo "deb http://repo.mysql.com/apt/ubuntu/ bionic mysql-8.0" > /etc/apt/sources.list.d/mysql.list

# This section installs the actual dependencies for. Git is a implicit dependency of "composer" which we will need to
# install later on as well. Curl we need to download stuff from the internet. Unzip we obviously need to unzip the
# uploads folder later on. The mysql client we need to migrate the database (It is a implicit dependency of
# "wp db import"). And python we need for the script which waits for the database.
RUN apt-get update && \
    apt-get install -y git && \
    apt-get install -y curl && \
    apt-get install -y unzip && \
    apt-get install -y mysql-community-client &&\
    apt-get install -y python3 && \
    apt-get install -y python3-pip

# "pymysql" is a python package which enables connections to mysql databases.
# https://pypi.org/project/PyMySQL/
RUN python3 -m pip install pymysql

# ! Important Info:
# So the "worpress:latest" container works like this: The actual source code etc is located in the folder
# "/usr/src/wordpress". But that is not the folder which is served by apache. Apache serves the folder "/var/www/html".
# The contents of the first folder are copied into the second one only at runtime of the container.
RUN ls -a /usr/src/wordpress && \
    ls -a /var/www/html
ENV WP_FOLDER="/usr/src/wordpress"
ENV WP_PLUGINS_FOLDER="$WP_FOLDER/wp-content/plugins"
ENV WP_THEMES_FOLDER="$WP_FOLDER/wp-content/themes"
# Another strange quirk of the wordpress container is that there is not "wp-config.php" file within the wordpress
# installation folder. This would cause an error. The correct configuration file is "wp-config-docker.php", but this
# name is not recognized by wp, so we have to rename it.
RUN cp "$WP_FOLDER/wp-config-docker.php" "$WP_FOLDER/wp-config.php"

# === (2) COPY CUSTOM FILES INTO THE CONTAINER ===
# Copy the custom apache configuration
# This mainly includes the changing of port 80 to port 8080
COPY ./apache2/ports.conf /etc/apache2/ports.conf
COPY ./apache2/000-default.conf /etc/apache2/sites-enabled/000-default.conf
# Copy the custom files which are needed to operate this container
COPY ./run.sh "$WP_FOLDER/run.sh"
COPY ./wait_for_mysql.py "$WP_FOLDER/wait_for_mysql.py"
# The backup of the sites state.
COPY ./wordpress.sql "$WP_FOLDER/wordpress.sql"
COPY ./uploads.zip "$WP_FOLDER/uploads.zip"
# Custom plugins and themes folder for our own wordpress instance
COPY ./plugins $WP_PLUGINS_FOLDER
COPY ./themes $WP_THEMES_FOLDER

# === (3) INSTALL ADDITIONAL APPLICATIONS ===
# We need the PHP package manager composer and the wordpress command line tools wpcli as additional tools for this
# wordpress installation, since they are not available as system packages, they have to be installed manually.
# These additional tools will be installed (arbitrarily chosen) into the following folder:
ENV UTIL_FOLDER="/home/util"
RUN mkdir $UTIL_FOLDER

# -- INSTALLING COMPOSER --
# https://getcomposer.org/download/
# Composer is a package manager for the PHP programming language. The held worpress plugin relies on composer to
# install it's dependencies. We will install
ENV COMPOSER_PATH="$UTIL_FOLDER/composer.phar"
ENV COMPOSER_COMMAND="php $COMPOSER_PATH"
RUN cd $UTIL_FOLDER && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=$UTIL_FOLDER && \
    php -r "unlink('composer-setup.php');"
RUN $COMPOSER_COMMAND --version

# -- INSTALLING WORDPRESS CLI --
# https://make.wordpress.org/cli/handbook/guides/installing/
# wp-cli is a command line interface for a wordpress installation. It can for example be used to activate plugins or
# themes from the command line, without having to visit the web interface
ENV WPCLI_PATH="$UTIL_FOLDER/wp-cli.phar"
ENV WPCLI_COMMAND="php $WPCLI_PATH --allow-root --path=$WP_FOLDER "
RUN cd $UTIL_FOLDER && \
    curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    ls -a && \
    $WPCLI_COMMAND --version && \
    $WPCLI_COMMAND --info

# === (4) INSTALLING THE CUSTOM CODE ===
# This wordpress installation heavily relies on a custom coded plugin called "helmholtz-plugin". This is not an
# official plugin, but instead only available as a git repository. As such it needs to be installed with a few
# extra steps. Those namely are the installation of it's dependencies using the composer package manager.

# -- INSTALLING THE PLUGINS --
ENV HELMHOLTZ_PLUGIN_FOLDER="/usr/src/wordpress/wp-content/plugins/helmholtz-plugin"
ENV HELMHOLTZ_PLUGIN_GIT="https://fuzzy.fzk.de/gogs/jonas.teufel/helmholtz-plugin.git"

# COPY ./plugins $WP_PLUGINS_FOLDER
RUN cd $WP_PLUGINS_FOLDER && \
    git clone $HELMHOLTZ_PLUGIN_GIT && \
    ls -a $WP_PLUGINS_FOLDER

# Here we use the composer package manager to install the dependencies for the plugin PHP code. This will generate
# autoload files so that all the imports work within the PHP source code
RUN cd "$WP_PLUGINS_FOLDER/helmholtz-plugin" && \
    $COMPOSER_COMMAND install

# Removing the default plugins, which are not needed
RUN rm -r $WP_PLUGINS_FOLDER/akismet && \
    rm "$WP_PLUGINS_FOLDER/hello.php"

# === (5) SETTING UP THE CONTAINER EXECUTION ===
# This is important to make the whole thing work with OpenShift: OpenShift does not
# allow the usage of port 80! We have to use 8080 and internally OpenShift redirects external access to
# port XXX towards the 8XXX range of the services...
EXPOSE 8080

# This right here is a workaround for the major annoynace of OpenShift. OpenShift uses an arbitrary user ID for the
# execution of each container. As such you would have to invest a lot of pain and effort to make sure that all
# folders would permit this kind of arbitrary user. The workaround is to just permit everyone to edit this folder and
# its contents. This is bad security practice, but it is what it is.
RUN chmod -R 0777 "$WP_FOLDER"

CMD bash -c "$WP_FOLDER/run.sh"