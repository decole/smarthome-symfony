package main

import (
	"context"
	"encoding/json"
	"fmt"
	mqtt "github.com/eclipse/paho.mqtt.golang"
	"github.com/joho/godotenv"
	amqp "github.com/rabbitmq/amqp091-go"
	"log"
	"os"
	"time"
)

var messagePubHandler mqtt.MessageHandler = func(client mqtt.Client, msg mqtt.Message) {
	fmt.Printf("Received message: %s from topic: %s\n", msg.Payload(), msg.Topic())
}

var messageHandler mqtt.MessageHandler = func(client mqtt.Client, msg mqtt.Message) {
	sendToRabbitMQ(msg)
}

func sendToRabbitMQ(msg mqtt.Message) {
	mapData := map[string]string{"topic": msg.Topic(), "payload": string(msg.Payload())}
	jsonString, _ := json.Marshal(mapData)

	conn, err := amqp.Dial(goDotEnvVariable("RABBITMQ_DSN_BRIDGE"))
	failOnError(err, "Failed to connect to RabbitMQ")
	defer conn.Close()

	ch, err := conn.Channel()
	failOnError(err, "Failed to open a channel")
	defer ch.Close()

	args := make(amqp.Table)
	args["x-message-ttl"] = int64(60000)

	q, err := ch.QueueDeclare(
		goDotEnvVariable("MQTT_PAYLOADS_RECEIVE_QUEUE"), // name
		true,  // durable
		false, // delete when unused
		false, // exclusive
		false, // no-wait
		args,  // arguments
	)
	failOnError(err, "Failed to declare a queue")
	ctx, cancel := context.WithTimeout(context.Background(), 5*time.Second)
	defer cancel()

	err = ch.PublishWithContext(ctx,
		"",     // exchange
		q.Name, // routing key
		false,  // mandatory
		false,  // immediate
		amqp.Publishing{
			ContentType: "application/json",
			Body:        jsonString,
		})
	failOnError(err, "Failed to publish a message")
}

var connectHandler mqtt.OnConnectHandler = func(client mqtt.Client) {
	fmt.Println("Connected")
}

var connectLostHandler mqtt.ConnectionLostHandler = func(client mqtt.Client, err error) {
	fmt.Printf("Connect lost: %v", err)
}

func failOnError(err error, msg string) {
	if err != nil {
		log.Panicf("%s: %s", msg, err)
	}
}

func main() {
    time.Sleep(20 * time.Second)

	opts := mqtt.NewClientOptions()
	opts.AddBroker(fmt.Sprintf("tcp://%s:%s", goDotEnvVariable("MQTT_BROKER_URL"), goDotEnvVariable("MQTT_PORT")))
	opts.SetClientID(goDotEnvVariable("CLIENT_ID"))
	opts.SetUsername(goDotEnvVariable("USER"))
	opts.SetPassword(goDotEnvVariable("PASSWORD"))
	opts.SetDefaultPublishHandler(messagePubHandler)
	opts.OnConnect = connectHandler
	opts.OnConnectionLost = connectLostHandler
	opts.SetPingTimeout(1 * time.Second)
	client := mqtt.NewClient(opts)

	if token := client.Connect(); token.Wait() && token.Error() != nil {
		panic(token.Error())
	}

	sub(client)

	for {
		time.Sleep(time.Second)

		if token := client.Connect(); token.Wait() && token.Error() != nil {
			panic(token.Error())
		}
	}
}

func sub(client mqtt.Client) {
	topic := goDotEnvVariable("SUBSCRIBE_TOPIC")
	token := client.Subscribe(topic, 1, messageHandler)
	token.Wait()
}

func goDotEnvVariable(key string) string {
	err := godotenv.Load(".env")

	if err != nil {
		log.Fatalf("Error loading .env file")
	}

	return os.Getenv(key)
}
