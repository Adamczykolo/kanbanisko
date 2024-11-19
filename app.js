const { Client } = require('pg')
var fs = require('fs');
var http = require('http');

function onRequest(request, response) {
    response.writeHead(200, { 'Content-Type': 'text/html' });

    // Query to fetch the first user's name
    client.query(`SELECT name FROM users`, (err, res) => {
        if (err) {
            response.writeHead(500);
            response.write('Database error');
            response.end();
            return;
        }

        const name = res.rows[0]?.name;

        // Read and modify the HTML file
        fs.readFile('./index.html', 'utf8', function (error, data) {
            if (error) {
                response.writeHead(404);
                response.write('File not found');
            } else {
                // Replace placeholder with actual name
                const updatedData = data.replace('$name', name);
                response.write(updatedData);
            }
            response.end();
        });
    });
}

http.createServer(onRequest).listen(3000, () => {
    console.log("serwer zapierdala na http://localhost:3000")
});

const client = new Client({
    host: "localhost",
    user: "postgres",
    port: "5432",
    password: "zaq1@WSX",
    database: "kanban"
})

client.connect();

