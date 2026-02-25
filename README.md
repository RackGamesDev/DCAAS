# DCAAS
Democracy As A Service, una API de votaciones desarrollada en Laravel

# INSTRUCCIONES PARA EL DESPLIEGUE
## Requerimientos
- Docker instalado, no es necesario tener Kubernetes. Al desplegar se descargarán imágenes adicionales.
- Un visor de bases de datos compatible con MariaDB, como DBeaver o DBVis
- A poder ser ejecutar en una máquina Linux, ya que los scripts de despliegue automático están pensados en Bash
- Firefox (el script de despliegue automático abre la página con firefox mediante el comando, esto se puede eliminar a gusto, habría que abrir manualmente http://localhost:8081/)
## Pasos (versión automática)
- Ejecutar: `sh up.sh`
- Por defecto se formará el entorno Docker y se abrirá la página en Firefox, si esto último falla, abrir http://localhost:8081/
- Es posible que haya que ejecutar algunos de los comandos comentados en dicho script

# DOCUMENTACIÓN
Para más información, consultar docs/Documentacion.pdf o mirar los comentarios del código´

## License
This project is licensed under the GNU General Public License v3.0.  
See the [LICENSE](./LICENSE.txt) file for details.
