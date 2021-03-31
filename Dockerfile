FROM wordpress:latest

MAINTAINER Jonas Teufel <jonseb1998@gmail.com>

#RUN apt-get update && \
#    apt-get install -y wget && \
#    apt-get install -y lsb-release && \
#    apt-get install -y gnupg && \
#    wget "http://repo.mysql.com/mysql-apt-config_0.8.10-1_all.deb" && \
#    dpkg -i "mysql-apt-config_0.8.10-1_all.deb"

COPY ./mysql-signature /tmp/mysql-signature

RUN apt-get update && \
    apt-get install -y gnupg && \
    apt-key add /tmp/mysql-signature && \
    echo "deb http://repo.mysql.com/apt/ubuntu/ bionic mysql-8.0" > /etc/apt/sources.list.d/mysql.list && \
    apt-get update && \
    apt-get install -y git && \
    apt-get install -y curl && \
    apt-get install -y unzip && \
    apt-get install -y mysql-community-client &&\
    apt-get install -y python3-pip

RUN python3 -m pip install pymysql

# Within the container, the wordpress installation is located in "/usr/src/wordpress"
# But apparantly the relevant wp-content folder is located in "/var/www/html"
RUN ls -a /usr/src/wordpress
RUN ls -a /var/www/html

ENV WP_FOLDER="/usr/src/wordpress"
ENV WP_PLUGINS_FOLDER="$WP_FOLDER/wp-content/plugins"
ENV WP_THEMES_FOLDER="$WP_FOLDER/wp-content/themes"
RUN cat "$WP_FOLDER/wp-config-docker.php"
RUN cp "$WP_FOLDER/wp-config-docker.php" "$WP_FOLDER/wp-config.php"

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

RUN chmod -R 0777 "$WP_FOLDER"

# == INSTALLING COMPOSER ==
# https://getcomposer.org/download/
# Composer is a package manager for the PHP programming language. The held worpress plugin relies on composer to
# install it's dependencies.
ENV UTIL_FOLDER="/home/util"
ENV COMPOSER_PATH="$UTIL_FOLDER/composer.phar"
ENV COMPOSER_COMMAND="php $COMPOSER_PATH"
RUN mkdir $UTIL_FOLDER

RUN cd $UTIL_FOLDER && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=$UTIL_FOLDER && \
    php -r "unlink('composer-setup.php');"
RUN $COMPOSER_COMMAND --version

# == INSTALLING WORDPRESS CLI ==
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

# == INSTALING THE THEMES ==
COPY ./themes $WP_THEMES_FOLDER
RUN ls -a $WP_THEMES_FOLDER

#RUN $WPCLI theme activate helmholtz-theme

# == INSTALLING THE PLUGINS ==
ENV HELMHOLTZ_PLUGIN_FOLDER="/usr/src/wordpress/wp-content/plugins/helmholtz-plugin"
COPY ./plugins $WP_PLUGINS_FOLDER
RUN ls -a $WP_PLUGINS_FOLDER

# Here we use the composer package manager to install the dependencies for the plugin PHP code. This will generate
# autoload files so that all the imports work within the PHP source code
RUN cd "$WP_PLUGINS_FOLDER/helmholtz-plugin" && \
    $COMPOSER_COMMAND install

# Removing the default plugins, which are not needed
RUN rm -r $WP_PLUGINS_FOLDER/akismet && \
    rm "$WP_PLUGINS_FOLDER/hello.php"

# This is important to make the whole thing work with OpenShift: OpenShift does not
# allow the usage of port 80! We have to use 8080 and internally OpenShift redirects external access to
# port XXX towards the 8XXX range of the services...
EXPOSE 8080
RUN ls -a /etc/apache2 && \
    ls -a /etc/apache2/sites-enabled && \
    cat /etc/apache2/ports.conf && \
    cat /etc/apache2/sites-enabled/000-default.conf

CMD bash -c "$WP_FOLDER/run.sh"