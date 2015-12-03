#!/bin/bash

#
# Setup environment so that we can find d4d binary
#

. 'functions.sh'

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Add environment path
export PATH=$SCRIPT_DIR:$PATH

# On Mac OS X turn on docker machine
OS=`uname`
if [ $OS = 'Darwin' ]
then
    DOCKER_MACHINE=`which docker-machine`
    exec "$DOCKER_MACHINE start default"
    exec "$(docker-machine env default)"
    exec "$DOCKER_MACHINE ip default"
fi

