const express = require("express")
const fs = require('fs');

var app = express()
app.get("/", function (request, response) {
    fs.readFile('index.html', null, function (error, data) {
        if (error) {
            response.writeHead(404);
            response.write("file not found!");
        }
        else {
            response.write(data);
        }
    })
})
app.listen(10000, function () {
    console.log("Started application on port %d", 10000)
});