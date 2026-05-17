#!/usr/bin/env sh

PORT_TO_USE=${PORT:-8080}
vendor/bin/server run 0.0.0.0:$PORT_TO_USE --adapter=App\\AppKernelAdapter --workers=-1


