# Using Wordpress on OpenShift with Docker

Manually installing wordpress (or any other web service for that matter) can be a real pain. Sure, in an ideal world 
that would not necessarily be the case, once you have a set of setup scripts etc. it should be rather straight 
forward. But a big part of what makes it so difficult is vastly or even marginally different operating systems and 
environment configurations which require a series of adjustments and workarounds. This is why it is generally a good 
idea to use containerized solutions in the form of Docker containers. Once a docker container works, the chances are 
high that it will also work in a different environment because the application code is basically encapsulated in its 
own virtual machine.

OpenShift is a server environment for hosting such containerized solutions. The following document will outline one 
strategy of deploying a wordpress installation to a OpenShift cluster.

## Problem outline

Before any of the technical details, the first question should be "What problem do we want to solve?"

For this consider the following scenario for the purpose of this explanation: We have an existing wordpress 
installation on some server. The server is being shut down and we want to migrate our setup to containerized solution 
to deploy it on a new OpenShift cluster we have access to. How would we have to set up this container? How do we 
migrate this site to OpenShift? (Obviously this also applies to how to create a new site from scratch for OpenShift 
deployment) The main challenges in terms of migrating an existing site are the following:

- How can the active plugins and themes be installed within the container wordpress instance automatically?
- How can we automatically install a potential custom plugin with our own custom code?
- How do we transfer both the uploaded media files and the existing database entries into the container?

Our main objective will be to find out how we have to set up this container, especially focusing on a workflow which 
involves the least amount of manual effort, once the container is set up!

## Important aspects when working with OpenShift

Sadly, OpenShift is not 100% compatible with "default" docker applications. OpenShift has made some design choices, 
which have to be explicitly kept in mind when setting up a dockerized wordpress solution. The most important aspects, 
which have greatly affected this solution, are the following:

- OpenShift uses arbitrary user ID's to execute a container. This is a security feature of OpenShift and probably a 
  good idea, but also a real pain to work with. Usually a container would always be executed as the same user. This 
  allows to simply assign this user to a certain group or make this user the owner of some files. Sometimes this user 
  may even have "sudo" rights (which are always useful, although it is bad practice). For OpenShift this is not the 
  case. Usually, the exact user ID which will execute the container is not known in beforehand. Additionally, this 
  user basically will never have "sudo" rights. This makes it a lot harder to figure out file and excecution 
  permissions, which have to be suitable for arbitrary users.
- OpenShift also does not allow services to operate on certain ports. Usually a wordpress web server would operate 
  on port 80 (HTTP) or 443 (HTTPS). Both of these ports are not permitted by OpenShift. That does not mean however, 
  that these standard ports cannot be used "from the outside". When OpenShift receives a request to port 80 for example, 
  it will be redirected internally to a service running at port 8080. So if you want to use a HTTP service, it's port 
  will have to be 8080.
  
## Container overview

To deploy a containerized wordpress installation, there will have to be two containers: One which contains the actual 
wordpress files and which operates the web server (such as apache or nginx) and one container with the required mysql 
database.

The mysql container can be used pretty much off the shelf. [Docker Hub](https://hub.docker.com/) provided a variety of 
options for mysql containers in different versions and environments.

The interesting part is the wordpress container. This will be the container we actually have to customize. For this 
purpose we create a new git repository, which will be the source from which our site will be constructed. Within this 
repository, we create the most important file which is the `Dockerfile`. Such a dockerfile contains instructions how 
to construct/build a new docker container. It will consequently contain all the instructions we specifically need to 
make our wordpress installation work. OpenShift offers a feature where we can give it the URL of a git repository and 
OpenShift will automatically clone this repository, search for a dockerfile and build a new container based on the 
instructions within. This is the mechanism we will use later on to actually create the application within OpenShift. 

One important detail to mention here is, that docker containers can build on top of each other. So within the 
dockerfile instructions it is possible to specify another already existing standard container (from Docker Hub, the 
local system etc...) which is to be used as the base for all further instructions. This is important in this case, 
because we will use the `wordpress:latest` container as the base of our own. This has two implications:

- We don't have to manually worry about the right version of Wordpress. Whenever the container is build, it will use 
  the latest available version right away.
- We don't actually have to worry about installing Wordpress at all. In fact, this base container already does the 
  majority of the work for us. As a base, it already comes with the wordpress source code already installed and even 
  with an apache web server readily configured to be used (although not OpenShift compatible, which we'll have to 
  change)!

## Constructing the dockerfile for our own wordpress

In this section I first want to outline the basic idea for the construction of the dockerfile. This isn't a detailed 
explanation of each and every single instruction, but rather the rough purpose of each section. Generally these 
sections are also documented as comments within the dockerfile, so for more detail I refer to reading the dockerfile 
itself.

So, what are the individual steps that are realized within the dockerfile for making the wordpress work?

1) **Installing system dependencies.** The wordpress base container, which we use, is in turn at some point derived 
   from base container which only contains the debian operating system. Thus, the container itself is also basically 
   a debian virtual machine. The first step for the installation of our own site is to install the necessary debian 
   system packages mainly with the `apt-get install` utility.
2) **Copying custom files into the container.** Our own wordpress site differs from a generic/fresh installation in 
   part through all the additional files that have accumulates through the customizations. For wordpress an important 
   example would be the installed plugins and themes, which are represented by folder of code within the wordpress 
   folder structure. We will place these additional files into the git repository as well and then during this step 
   these files are being copied into the virtual environment of the container.
3) **Installing additional dependencies.** Aside from wordpress we will need to install some additional tools, which 
   are sadly not as easily available as a system package. These tools will have to be installed by other means and with 
   some additional effort. The tools we need are *composer* and *wpcli*. Composer is a PHP package manager, it is used 
   to organize, install & update dependencies of PHP projects. We mainly need this tool to install our custom plugin 
   code, which organizes it's dependencies as a composer lock file. Wpcli is a command line access tool for managing 
   wordpress installations. Common taks such as installing, updating and activating plugins can be performed as a 
   terminal commands instead of having to manually visit the web backend.
4) **Installing custom code.** During this step we will use composer to install the custom plugin code.
5) **Configuring the container execution.** As the last step within the dockerfile we will configure how the container 
   will act when it is actually started. That is the commands it should execute to actually serve the final wordpress 
   site to the user.
   
Something, which will not happen during the build phase of the container (within the dockerfile) is the actual 
installation of wordpress and the migration of the database backup. It actually cant happen during the build process 
because the installation needs a connection to the database, which potentially does not exist during the build. This 
means this installation will have to take place during the runtime of the container. The solution is to have the 
container check at every startup if everything is correctly installed and if that is not the case perform the 
installation first before starting the web server.

### 1. System dependencies

The following packages are the most important system dependencies required for our method of setting up the wordpress 
container:

- `unzip`: We will migrate the uploaded media files of our own installation as a ZIP file and 
  thus there needs to be a possibility to unzip this file.
- `curl`: The additional dependencies composer and wpcli need to be installed from a package, which needs to 
  be downloaded from the Internet. Curl is used to perform this download.
- `mysql-community-client`: While the mysql server is not in this container, a client is still needed to communicate 
  with the database. Specifically, the client is need to perform the database migration.
- `python3`: We will use a custom python script to delay the execution of the container until it is certain, that the 
  database container is online as well.

### 2. Custom files and folders

The following folders will be needed to be copied from the repository into the container:

- *plugins*: This folder represents the plugins folder of the wordpress installation. It contains one subfolder for 
  each plugin to be used within our wordpress site. (! The mere existence of this folder does not mean that they are 
  active by default !). It should replace the folder `{wp-root}/wp-content/plugins`.
- *themes*: This folder represents the themes folder of the wordpress installation. (! These will also not be active on 
  default). It should replace `{wp-root}/wp-content/themes`.

The following files will be needed to be copied from the repository into the container:

- *wordpress.sql*: This file is database backup we want to migrate to our new application. It contains SQL instructions 
  which reconstruct the sql database of the previous installation / instance. It is obtained by running 
  a [mysqldump](https://mariadb.com/kb/en/mysqldump/) on the old server.
- *uploads.zip*: The compressed verion of the uploads folder of the old wordpress instance to be migrated. While the 
  wordpress database contains mostly configuration and the textual content of the posts, the uploads folder contains 
  all the images and generally file-related resources. It is obtained by zipping up the `{wp-root}/wp-content/uploads` 
  folder of the old site.
- *ports.conf*, *000-default.conf*: These are configuration files for the apache web server. Usually the web server 
  which comes with the `wordpress:latest` container works out of the box, but we need to change the port of operation 
  to 8080 to make it compatible with OpenShift.
- *wait_for_mysql.py*: The python script, which waits until a database connection can be established.
- *run.sh*: The terminal script which is executed for the actual runtime of the container. It's contents will be 
  discussed later.
  
  
  
---

## Appendix

### Why not S2I?

OpenShift offers different ways to approach the deployment of an application. For the deployment of a non standard 
application (a prebuild - off the shelf container like mysql, Jenkins, GitLab) there are essentially two options: 

1) Building a custom docker image using a dockerfile.
2) Using s2i (source to image)

The latter option at first seems to be more convenient, because one essentially does not have to be concerned with 
the specifics of how containers work. OpenShift also offers a prebuild wordpress or php container. With S2I, you can 
choose one of these prebuild containers and specify a git repository as well. The content of this repository are then
automatically copied into the container. There is also the option to specify a script to be executed before the 
container starts, a sort of "startup" script. The process is somewhat similar to the dockerfile solution but less 
involved with the details. A simpler solution would obviously be preferable.

But there is a general problem with using s2i: It is simpler but equally less flexible in many ways. The main issue 
which I have encountered is that the startup script is imposed with the same restrictions as any application code: It 
does not have superuser perimissions and thus no access to certain commands and folder. This makes it indefinitely 
harder to install additional tools and code, which is not related to the singular purpose of your application. An 
example would be that there are no permissions to install additional system packages. So if your application needs an 
additional dependency for some reason the only chance is to hope that it exists within the prebuild base container. If 
that is not the case, than that already marks the limitations of using s2i for your application.

### Why not `default-mysql-client` instead of the hassle with the community edition?

Installing the `mysql-community-client` is quite some additional hassle, because we have to add the according PPA 
repository first. This seems unnecessary when the `default-mysql-client` is available on default. In the end it comes 
down to basically the same thing. But there is one edge case where the default client does not work:

Imagine you have a rather old mysql backup of your database which you want to use to migrate the data to your new 
containerized application. There exists a bug where old versions of wordpress databases have some peculiar setting 
which specifies the encoding of some cache or smth. like that. This causes the default mysql client to crash when 
attempting to migrate the backup. The community edition however works!