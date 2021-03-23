FROM wordpress:latest
MAINTAINER Jonas Teufel <jonseb1998@gmail.com>

RUN apt-get update && \
    apt-get install -y git && \
    apt-get install -y curl && \
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

COPY ./run.py "$WP_FOLDER/run.py"
COPY ./run.sh "$WP_FOLDER/run.sh"
COPY ./wait_for_mysql.py "$WP_FOLDER/wait_for_mysql.py"

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



# ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
# IMPORTANT NOTE:
# After some playing around, I have found out, that it is actually crucial to provide the CMD in this format
# (using the brackets) and not in shell format. When attempting to use the shell format, the server returns
# "404 Forbidden" when attempting to access. I dont know why though...
#CMD ["apache2-foreground"]
CMD bash -c "$WP_FOLDER/run.sh"
#CMD bash -c "python3 $WP_FOLDER/wait_for_mysql.py && \
#             $WPCLI_COMMAND core install --url=0.0.0.0 --title=HeldTest --admin_user=Jonas --admin_password=Jonas --admin_email=jonseb1998@gmail.com && \
#             $WPCLI_COMMAND plugin activate helmholtz-plugin && \
#             $WPCLI_COMMAND theme activate helmholtz-theme && \
#             apache2-foreground"