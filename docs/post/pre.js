const token = insomnia.environment.get('AuthorizationToken');

if (token) {
	insomnia.collectionVariables.set("dynamic_token", token);
    //insomnia.request.auth.update({type: 'bearer', bearer: [{key: 'token', value: "token"}]}, 'bearer');
} else {
    console.error("Authorization Token is missing!");
}
