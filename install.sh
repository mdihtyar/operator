#!/bin/bash

set -e

cd $(dirname $0)
# Variables
ANSIBLE_VERSION="2.4"
VERSION=$([ -e version ] && echo $(cat version)$(printf ".";git rev-parse --short=16 HEAD || echo "") || echo "unknown")

# Common functions
# ------------------------------------------------------------------------------
function msg {
    if [ "${OUTPUT_WITH_NEW_LINE}" == true ]; then
        echo -e "\n$1\n"
    else
        echo "$1"
    fi
    if [ ! -z $2 ]; then exit $2; fi
    return 0
}

function error {
    msg "ERROR! $1" 1
}
function install_requirements {
    # Check is python command reachable.
    if [ "$(which python)" == "" ]; then
        apt-get update
        apt-get install -y python
    fi
    # Installing ansible
    if [ "$(which ansible)" == "" ]; then
        apt-get update
        apt-get install -y python-pip
        pip install pip --upgrade
        pip install ansible==${ANSIBLE_VERSION}
    elif [ "$(ansible --version | head -1 | awk '{ print $2 }' | grep "^${ANSIBLE_VERSION}")" == "" ]; then
        echo -e "Current ansible version is: $(ansible --version | head -1)\nInstalling requiremented ansible==${ANSIBLE_VERSION}"
        pip install ansible==${ANSIBLE_VERSION}
    fi
    ansible-playbook -i provision/inventory provision/requirements.yml
    return 0
}
# ------------------------------------------------------------------------------

msg "Installation of operator application"

printf "Checking OS. "
if [ "$(lsb_release -i -s | tr [:upper:] [:lower:])" == "ubuntu" ] && [ "$(lsb_release -c -s)" == "xenial" ] || [ "$(lsb_release -c -s)" == "trusty" ] ; then
     echo "OK"
else
    error "Provided operation systems is not suitable for this application"
fi

printf "Installing required software. "
install_requirements &> /dev/null
if [ "$?" != 0 ]; then
    error "Required software can not be installed. Please check."
fi
echo "OK"

echo "Deployment of the application."
cd provision
ansible-playbook -i inventory --extra-vars="{version: ${VERSION}}" setup.yml
