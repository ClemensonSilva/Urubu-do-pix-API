
# Urubu-do-pix

# Descrição
Esta API tem como objetivo simular o funcionamento do golpe que ficou conhecido como
Urubu do Pix, sendo utilizada apenas como forma de treinar habilidades de desenvolvimento
e organização de código.

## Authentication
Não é nescessária.

## Pré-requisitos
Docker instalado em sua máquina

## Instalação
Para rodar o projeto, clone este repositório e vá até a pasta onde ele foi clonado
e rode os comandos

```
docker compose build
docker compose up
```
logo em seguida, use algum software de teste de apis para acessar os endpoints.

## API Endpoints

#### Criar usuário
```http
POST /create/user
```

**Request Body**
```json
{
  "user_name": "Germany Goverment",
	"user_balance": 2000
}
```

**Response**
```json
{
	"sucess": true,
	"message": "User created corretly"
}
```

#### Listar todos os usuários
```http
GET /user
```

**Request Body**
```json
{
  "user_name": "German Goverment",
	"user_balance": 2000
}
```

**Response**
```json
{
	"data": [
		{
			"id": 1,
			"user_name": "German Goverment",
			"user_balance": 2000
		}
	]
}

```
#### Deposito na conta do usuário
```http
POST /deposit
```

**Request Body**
```json
{
	"user_id": 1,
	"deposit":10000
}
```

**Response**
```json
{
	"sucess": true,
	"message": " Deposit made successfully"
}
```

#### Investimento na plataforma
```http
POST /transaction
```

**Request Body**
```json
{
  "user_id": 1,
	"depositValue": 100,
	"investimentTime":7
}
```

**Response**
```json
{
	"sucess": true,
	"message": "Transaction made sucessfuly"
}

```
#### Investimento na plataforma
```http
POST /withdraw
```

**Request Body**
```json
{
	"user_id": 1,
	"transaction_id": 1,
	"valueToWithdraw": 2
}
```

**Response**
```json
{
	"sucess": true,
	"message": "Withdraw made sucessfully"
}
```

#### Informação de determinado investimento
```http
POST /transactionInformation
```

**Request Body**
```json
{
  "user_id": 1,
  "transaction_id":1
}
```

**Response**
```json

{
	"transaction_id": 1,
	"user": {
		"id": 1,
		"user_name": "German Goverment"
	},
	"totalInvested": 98,
	"profit": 0,
	"depositValue": 98,
	"interest": "0.4 per months",
	"depositDate": {
		"date": "2025-01-07 00:00:00.000000",
		"timezone_type": 3,
		"timezone": "UTC"
	}
}
```


#### Investimentos do usuário na plataforma
```http
POST /user/investiments
```

**Request Body**
```json
{
	"user_id": 1
}
```

**Response**
```json
{
"German Goverment",
	{
		"transaction_id": 1,
		"totalInvested": 98,
		"profit": 0,
		"depositValue": 98,
		"interest": "0.4 per months",
		"depositDate": {
			"date": "2025-01-07 00:00:00.000000",
			"timezone_type": 3,
			"timezone": "UTC"
		}
	},
	{
		"transaction_id": 2,
		"totalInvested": 110.62,
		"profit": 10.62,
		"depositValue": 100,
		"interest": "0.4 per months",
		"depositDate": {
			"date": "2024-12-31 00:00:00.000000",
			"timezone_type": 3,
			"timezone": "UTC"
		}
	}
}
```

## Construido com

- PHP - linguagem de programação
- MySql - banco de dados
- Docker - container para desenvolvimento
