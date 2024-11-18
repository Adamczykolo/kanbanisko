const express = require("express");
const fs = require('fs');
const { Sequelize } = require('sequelize');
require("dotenv").config();

var app = express()

const sequelize = new Sequelize(
    process.env.DB_URL,
    {
        dialect: "postgres",
        protocol: "postgres",
        dialectOptions: {
            ssl: {
                requrie: true,
                rejectUnauthorized: false,
            },
        },
        logging: false,
    });

sequelize.sync().then(() => { console.log("Database connected") }).catch((err) => { console.log(err) });

app.get("/", function (request, response) {
    fs.readFile('index.html', null, function (error, data) {
        if (error) {
            response.writeHead(404);
            response.write("file not found!");
        }
        else {
            response.write(data);
        }
        response.end();
    })
})
app.listen(10000, function () {
    console.log("Started application on port %d", 10000)
});