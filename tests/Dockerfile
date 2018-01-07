ARG OS=ubuntu
ARG CODE=xenial

FROM ubuntu:${CODE}

LABEL mainteiner="devops@onix-systems.com"

RUN apt-get update && \
    apt-get install -y \
      git \
      lsb_release \
      sudo && \
    apt-get clean
