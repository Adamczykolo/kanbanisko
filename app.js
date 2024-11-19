const { Client } = require('pg')
var fs = require('fs');
var http = require('http');

http.createServer((request, response) => {
    client.query(`SELECT name FROM users LIMIT 1`, (err, res) => {
        const name = res?.rows[0]?.name || 'Guest';

        fs.readFile('./index.html', 'utf8', (error, data) => {
            const htmlContent = data?.replace('$name', name) || 'Hello Guest';

            response.writeHead(200, { 'Content-Type': 'text/html' });
            response.end(htmlContent);
        });
    });
}).listen(3000, () => {
    console.log('Server running at http://localhost:3000');
});


const client = new Client({
    host: "localhost",
    user: "postgres",
    port: "5432",
    password: "zaq1@WSX",
    database: "kanban"
})

client.connect();

