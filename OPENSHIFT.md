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
deployment)

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

---

## Appendix

### Why not S2I?

I tried, it does not work 