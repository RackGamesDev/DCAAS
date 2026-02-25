try{const responseBody = insomnia.response.json();
const token = responseBody?.data?.access_token ?? false;
if (token) insomnia.environment.set("AuthorizationToken", token);}catch(e){}
/*

insomnia.test("Correcto", () => {
	insomnia.expect(insomnia.response.code).to.equal(200);
});

*/

/*
const idEncuesta = responseBody?.data?.encuesta?.id ?? false;
if (idEncuesta) insomnia.environment.set("encuesta-presentacion", idEncuesta);
*/

/*
const idInforme = responseBody?.data?.informe_id ?? false;
if (idInforme) insomnia.environment.set("informe-presentacion", idInforme);
*/