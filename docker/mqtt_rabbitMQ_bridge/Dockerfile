FROM golang:1.19-alpine

WORKDIR /mqttToRabbit

COPY go.mod ./
COPY go.sum ./
COPY .env ./

RUN go mod download

COPY *.go ./

RUN go build -o /mqtt-rabbitmq

CMD [ "/mqtt-rabbitmq" ]