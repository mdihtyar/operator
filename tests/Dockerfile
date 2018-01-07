ARG OS=ubuntu
ARG CODENAME=xenial

FROM ${OS}:${CODENAME}

LABEL maintainer="devops@onix-systems.com"

RUN apt-get update && \
    apt-get install -y \
      git \
      lsb-release \
      sudo \
      systemd && \
    apt-get clean
