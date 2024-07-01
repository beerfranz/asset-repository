exports.handler = async (event, context, callback) => {

    const { CognitoIdentityProviderClient, DescribeUserPoolCommand } = require("@aws-sdk/client-cognito-identity-provider");

    const cognitoIdentityProviderClient = new CognitoIdentityProviderClient();

    const input = {
      UserPoolId: event.userPoolId,
    };

    const command = new DescribeUserPoolCommand(input);

    const response = await cognitoIdentityProviderClient.send(command);

    // TODO: replace by a count on unregistered users
    // remplace it by triggers + resync nightly + endpoints in user-service to have number of unregistrated users
    // https://stackoverflow.com/questions/69316247/aws-cognito-user-pool-total-users-count-and-querying
    // With cognito-local, this number is always 0
    if (response.UserPool.EstimatedNumberOfUsers > 10) {
        var error = new Error("Cannot register users because too much users");
        // Return error to Amazon Cognito
        callback(error, event);
    }
    // Return OK to signup
    callback(null, event);
};
