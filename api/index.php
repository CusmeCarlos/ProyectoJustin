<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Proyecto Justin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 900px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
        }
        .status {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            margin-bottom: 30px;
            border-radius: 5px;
        }
        .status.error {
            background: #ffebee;
            border-left-color: #f44336;
        }
        .status h3 {
            color: #4caf50;
            margin-bottom: 5px;
        }
        .status.error h3 {
            color: #f44336;
        }
        .endpoints {
            margin-top: 30px;
        }
        .endpoint-group {
            margin-bottom: 25px;
        }
        .endpoint-group h3 {
            color: #764ba2;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        .endpoint {
            background: #f5f5f5;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: transform 0.2s;
        }
        .endpoint:hover {
            transform: translateX(5px);
            background: #eeeeee;
        }
        .method {
            font-weight: bold;
            padding: 5px 12px;
            border-radius: 5px;
            margin-right: 15px;
            font-size: 0.85em;
            min-width: 60px;
            text-align: center;
        }
        .method.get { background: #4caf50; color: white; }
        .method.post { background: #2196f3; color: white; }
        .method.put { background: #ff9800; color: white; }
        .method.delete { background: #f44336; color: white; }
        .path {
            font-family: 'Courier New', monospace;
            color: #555;
            flex: 1;
        }
        .info-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin-top: 30px;
            border-radius: 5px;
        }
        .info-box h3 {
            color: #ff9800;
            margin-bottom: 10px;
        }
        .info-box ul {
            margin-left: 20px;
        }
        .info-box li {
            margin-bottom: 5px;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 API Proyecto Justin</h1>
        <p class="subtitle">API REST para el Sistema de Simulación de Pruebas de Admisión Universitaria</p>

        <?php
        require_once 'config/database.php';

        $database = new Database();
        $conn = $database->getConnection();

        if ($conn !== null) {
            echo '<div class="status">';
            echo '<h3>✅ Estado: Operativa</h3>';
            echo '<p>La conexión a la base de datos es exitosa</p>';
            echo '</div>';
        } else {
            echo '<div class="status error">';
            echo '<h3>❌ Error de Conexión</h3>';
            echo '<p>No se pudo conectar a la base de datos. Verifica tu configuración de XAMPP.</p>';
            echo '</div>';
        }
        ?>

        <div class="endpoints">
            <div class="endpoint-group">
                <h3>🔐 Autenticación</h3>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/endpoints/login.php</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/endpoints/register.php</span>
                </div>
            </div>

            <div class="endpoint-group">
                <h3>🎓 Universidades</h3>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/endpoints/universidades.php</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/endpoints/universidades.php</span>
                </div>
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/endpoints/universidades.php?id={id}</span>
                </div>
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/endpoints/universidades.php?id={id}</span>
                </div>
            </div>

            <div class="endpoint-group">
                <h3>📚 Carreras</h3>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/endpoints/carreras.php</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/endpoints/carreras.php</span>
                </div>
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/endpoints/carreras.php?id={id}</span>
                </div>
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/endpoints/carreras.php?id={id}</span>
                </div>
            </div>

            <div class="endpoint-group">
                <h3>❓ Preguntas</h3>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/endpoints/preguntas.php</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/endpoints/preguntas.php</span>
                </div>
                <div class="endpoint">
                    <span class="method put">PUT</span>
                    <span class="path">/endpoints/preguntas.php?id={id}</span>
                </div>
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/endpoints/preguntas.php?id={id}</span>
                </div>
            </div>

            <div class="endpoint-group">
                <h3>📊 Resultados</h3>
                <div class="endpoint">
                    <span class="method get">GET</span>
                    <span class="path">/endpoints/resultados.php</span>
                </div>
                <div class="endpoint">
                    <span class="method post">POST</span>
                    <span class="path">/endpoints/resultados.php</span>
                </div>
                <div class="endpoint">
                    <span class="method delete">DELETE</span>
                    <span class="path">/endpoints/resultados.php?id={id}</span>
                </div>
            </div>
        </div>

        <div class="info-box">
            <h3>📖 Documentación</h3>
            <ul>
                <li>Consulta el archivo <strong>README.md</strong> para documentación completa</li>
                <li>URL base: <code>http://localhost/ProyectoJustin/api/endpoints/</code></li>
                <li>Usuario de prueba: <code>admin@admin.com</code> / <code>123456</code></li>
                <li>Usa <strong>Postman</strong> para probar los endpoints</li>
            </ul>
        </div>
    </div>
</body>
</html>
