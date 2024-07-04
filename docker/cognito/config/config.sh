#!/bin/bash

pools=$(aws cognito-idp list-user-pools --max-results 20)

if [ -z "$pools" ]; then
  echo "Create pool"
  pools=$(aws cognito-idp create-user-pool \
    --pool-name test)
  poolId=$(echo $pools | awk '{ print $5 }' | head -1)
else
  poolId=$(echo $pools | awk '{ print $3 }' | head -1)
fi

aws cognito-idp update-user-pool --user-pool-id ${poolId} \
  --auto-verified-attributes "email" \
  --user-attribute-update-settings AttributesRequireVerificationBeforeUpdate=email
  # --account-recovery-setting RecoveryMechanisms=[{Priority=1,Name=verified_email}]

clients=$(aws cognito-idp list-user-pool-clients --user-pool-id ${poolId})

if [ -z "$clients" ]; then
  echo "Create client"
  clients=$(aws cognito-idp create-user-pool-client \
    --user-pool-id ${poolId} \
    --client-name test \
    --generate-secret
  )

fi

clientId=$(echo $clients | awk '{ print $2 }' | head -1)
clientSecret=$(aws cognito-idp describe-user-pool-client --user-pool-id $poolId --client-id $clientId | head -1 | awk '{ print $4 }')

# aws cognito-idp update-user-pool --user-pool-id $poolId --lambda-config '
# {
#   "PreSignUp": "arn:aws:lambda:::lambda-dev-function1:function1"
# }'

# Cognito-local don't manage resource servers
# resourceServers=$(aws cognito-idp list-resource-servers --user-pool-id ${poolId})

# if [ -z "${resourceServers}" ]; then
#   resourceServers=$(aws cognito-idp create-resource-server --user-pool-id ${poolId} --identifier http://localhost:3000  --name port3000)
# fi

echo "COGNITO_POOL_ID: $poolId"
echo "COGNITO_CLIENT_ID: $clientId"
echo "COGNITO_CLIENT_SECRET: $clientSecret"

# Example to create a user
# aws cognito-idp sign-up --client-id ${clientId} --username jane3@example.com --password PASSWORD \
#   --user-attributes Name="email",Value="jane3@example.com" Name="name",Value="Jane"

# $1 email
# $2 password
function createUser() {
  aws cognito-idp admin-get-user --user-pool-id $poolId --username $1 > /dev/null 2>&1

  if [[ $? == "254" ]]; then
    aws cognito-idp sign-up --client-id ${clientId} --username $1 --password $2 \
      --user-attributes Name="email",Value="$1" Name="name",Value="Foo" > /dev/null
  fi
}

createUser admin@local admin
createUser user@local user

echo "Users:"
aws --output text cognito-idp list-users --user-pool-id ${poolId}
