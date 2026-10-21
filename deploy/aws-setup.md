# Configuración AWS para el despliegue (OIDC + SSM)

Cuenta `078870416640`, región `us-east-1`, instancia `i-0658016bbbccf151f`
(ya tiene el instance profile `ubuntu-ssm-instance-profile` y el SSM Agent activo).

## 1. Proveedor OIDC de GitHub (una vez por cuenta)

Consola IAM > Identity providers > Add provider > OpenID Connect
- Provider URL: `https://token.actions.githubusercontent.com`
- Audience: `sts.amazonaws.com`

O con CLI:

```bash
aws iam create-open-id-connect-provider \
  --url https://token.actions.githubusercontent.com \
  --client-id-list sts.amazonaws.com
```

## 2. Rol `github-deploy-mitos`

Trust policy (solo la rama `main` de este repositorio puede asumirlo):

```json
{
  "Version": "2012-10-17",
  "Statement": [{
    "Effect": "Allow",
    "Principal": {
      "Federated": "arn:aws:iam::078870416640:oidc-provider/token.actions.githubusercontent.com"
    },
    "Action": "sts:AssumeRoleWithWebIdentity",
    "Condition": {
      "StringEquals": {
        "token.actions.githubusercontent.com:aud": "sts.amazonaws.com",
        "token.actions.githubusercontent.com:sub": "repo:fishercom/ajinomoto-mitos:ref:refs/heads/main"
      }
    }
  }]
}
```

Permissions policy (mínimo privilegio: solo ejecutar `AWS-RunShellScript` en esta instancia):

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": "ssm:SendCommand",
      "Resource": [
        "arn:aws:ssm:us-east-1::document/AWS-RunShellScript",
        "arn:aws:ec2:us-east-1:078870416640:instance/i-0658016bbbccf151f"
      ]
    },
    {
      "Effect": "Allow",
      "Action": ["ssm:GetCommandInvocation", "ssm:ListCommandInvocations"],
      "Resource": "*"
    }
  ]
}
```

## 3. Secrets de GitHub

| Secret | Valor |
|---|---|
| `AWS_ROLE_ARN` | `arn:aws:iam::078870416640:role/github-deploy-mitos` |
| `EC2_INSTANCE_ID` | `i-0658016bbbccf151f` |

## 4. Cerrar SSH

Una vez que el primer despliegue por SSM funcione, elimina la regla de entrada del puerto 22 del Security Group.
Para entrar al servidor en adelante: `aws ssm start-session --target i-0658016bbbccf151f` (requiere el plugin Session Manager).

## Notas

- `mitos-deploy` descarga el commit desde `codeload.github.com`, por lo que el repositorio debe ser público.
  Si pasa a privado hay que cambiar a un bucket S3 (el workflow sube el artefacto y el instance profile recibe `s3:GetObject`).
- Cualquier cambio en `deploy/mitos-deploy.sh` requiere reinstalarlo en el servidor (volver a ejecutar `provision.sh`);
  el workflow solo despliega el tema.
