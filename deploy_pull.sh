#!/usr/bin/env bash
# deploy_pull.sh — simple: ssh -> cd -> git pull

SSH_USER="test"
SSH_HOST="192.168.0.100"
REPO_DIR="/home/test/MarketLink-Web"
SSH_OPTIONS="-o StrictHostKeyChecking=no -o ConnectTimeout=10"

echo "Connecting to ${SSH_USER}@${SSH_HOST} and running git pull in ${REPO_DIR}..."

ssh ${SSH_OPTIONS} "${SSH_USER}@${SSH_HOST}" "cd '${REPO_DIR}' && echo 'On: ' \$(hostname) && git rev-parse --abbrev-ref HEAD || true && git pull"
EXIT_CODE=$?

if [ $EXIT_CODE -eq 0 ]; then
  echo "✅ git pull completed."
else
  echo "❌ git pull failed with exit code $EXIT_CODE"
fi

exit $EXIT_CODE