<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembrete de Agendamento - C3Saúde</title>
    <style>
        /* Fonte personalizada */
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
        
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            width: 150px;
            margin-bottom: 10px;
        }

        .header h1 {
            color: #5c6bc0;
            font-size: 24px;
            margin: 0;
        }

        .content {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .content p {
            margin: 10px 0;
        }

        .content strong {
            color: #5c6bc0;
        }

        .footer {
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer a {
            color: #5c6bc0;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .contact-info {
            text-align: left;
            margin-top: 20px;
        }

        .contact-info p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }

    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header">
            <img src="http://localhost:8000/img/logo.png" alt="Logo C3Saúde">
            <h1>Lembrete de Agendamento</h1>
        </div>

        <!-- Conteúdo -->
        <div class="content">
            <p>Olá, <strong>{{ $agendamento->nome_funcionario }}</strong>!</p>
            <p>Seu exame está agendado para <strong>{{ $agendamento->data_exame }}</strong> às <strong>{{ $agendamento->horario_exame }}</strong>.</p>
            <p><strong>Local:</strong> {{ $agendamento->clinica_agendada }}</p>
            <p>Não se esqueça de comparecer no horário.</p>
        </div>

        <!-- Rodapé -->
        <div class="footer">
            <p>Atenciosamente,<br><strong>C3Saúde</strong></p>

            <div class="contact-info">
                <p><strong>Matheus Martins</strong><br>
                T.I.<br>
                <a href="mailto:matheus.martins@c3saude.com.br">matheus.martins@c3saude.com.br</a><br>
                (11) 5197-5003 | (11) 91245-1078<br>
                R. Carnaubeiras, 168 – Cj. 131 e 132<br>
                CEP 04343-080 – Jabaquara – São Paulo – SP</p>
            </div>
        </div>
    </div>
</body>
</html>
