try{const responseBody = insomnia.response.json();
const token = responseBody?.data?.access_token ?? false;
if (token) insomnia.environment.set("AuthorizationToken", token);}catch(e){}
