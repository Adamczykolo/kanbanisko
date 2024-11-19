const { Client } = require('pg')
var fs = require('fs');
var http = require('http');

function onRequest(request, response) {
    response.writeHead(200, { 'Content-Type': 'text/html' });
    fs.readFile('./index.html', null, function (error, data) {
        if (error) {
            response.writeHead(404);
            response.write('File not found');
        } else {
            response.write(data);
        }
        response.end();
    })

}

http.createServer(onRequest).listen(3000);

const client = new Client({
    host: "localhost",
    user: "postgres",
    port: "5432",
    password: "zaq1@WSX",
    database: "kanban"
})

client.connect();

client.query(`SELECT * FROM users`, (err, res) => {
    if (!err) {
        console.log(res.rows);
    }
    else {
        console.log(err.message);
    }
    client.end;
})


