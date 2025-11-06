<?php
session_start();

// Configuración de encabezados para desarrollo
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Verificar que existen datos de registro en sesión
if (!isset($_SESSION['datos_registro'])) {
    // Si no hay datos, redirigir al formulario
    header('Location: micuenta.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pago con PayPal - PELIX</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .main-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #bb860b;
            font-size: 3em;
            margin: 0;
        }
        
        .payment-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background-color: #111;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(187, 134, 11, 0.2);
            border: 1px solid #bb860b;
            text-align: center;
        }

        .payment-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #bb860b;
        }

        .payment-header h2 {
            color: #bb860b;
            font-size: 2em;
            margin-bottom: 10px;
        }

        .user-info {
            margin-bottom: 30px;
            padding: 20px;
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            border: 1px solid #bb860b;
            text-align: left;
        }

        .user-info p {
            margin: 10px 0;
            color: #ccc;
        }

        .user-info strong {
            color: #bb860b;
        }

        #paypal-button-container {
            margin: 30px auto;
            max-width: 300px;
        }

        .btn-cancel {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background-color: transparent;
            color: #bb860b;
            border: 2px solid #bb860b;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            font-size: 1em;
        }

        .btn-cancel:hover {
            background-color: rgba(187, 134, 11, 0.1);
        }

        .payment-description {
            color: #ccc;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .payment-warning {
            background-color: #330000;
            color: #bb860b;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #bb860b;
            border-radius: 5px;
        }

        #loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: #bb860b;
            font-size: 1.2em;
        }

        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #bb860b;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .payment-container {
                padding: 20px;
            }
            
            .user-info {
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="logo">
            <h1>PELIX</h1>
        </div>

        <div class="payment-container">
            <div class="payment-warning">
                <strong>Importante:</strong> Por favor, no cambie de pestaña ni cierre esta ventana durante el proceso de pago.
            </div>
            
            <div class="payment-header">
                <h2>Finalizar Registro</h2>
                <p class="payment-description">Complete el proceso de pago para activar su suscripción premium</p>
            </div>
            
            <div class="user-info">
                <p><strong>Nombre:</strong> <?= htmlspecialchars($_SESSION['datos_registro']['nombre'] . ' ' . $_SESSION['datos_registro']['apellidos']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($_SESSION['datos_registro']['email']) ?></p>
                <p><strong>Plan:</strong> Suscripción Premium</p>
                <p><strong>Precio:</strong> 5,00 €/mes</p>
            </div>
            
            <div id="paypal-button-container"></div>

            <a href="micuenta.php" class="btn-cancel">Cancelar Registro</a>
        </div>
    </div>

    <!-- Cargar Overlay -->
    <div id="loading-overlay">
        <div class="loader"></div>
        <div>Procesando tu pago...</div>
    </div>

    <!-- Include the PayPal JavaScript SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=ATDIOIAkdJ68qVkpwdn0Zen5-KYzWUBMOeiX72V9Lf7TFJKR7nu6UiSlWSjoZt-PFFd6ILmsi0OMIUIl&currency=EUR"></script>

    <script>
        paypal.Buttons({

            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '5.00', // Coste de la suscripción
                            currency_code: 'EUR'
                        },
                        description: 'Suscripción mensual a PELIX'
                    }],
                    application_context: {
                        shipping_preference: 'NO_SHIPPING' // No se requiere envío
                    }
                });
            },

            onApprove: function(data, actions) {
                document.getElementById('loading-overlay').style.display = 'flex';
                return actions.order.capture().then(function(details) {
                    // Extraer solo los datos relevantes
                    fetch('guardar_paypal_session.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            paypal_payer_id: details.payer.payer_id,
                            paypal_order_id: data.orderID,
                            paypal_email: details.payer.email_address,
                            // Puedes añadir más campos aquí si los necesitas
                        })
                    }).then(() => {
                        window.location.href = 'completar_registro.php?paypal_success=1';
                    });
                });
            },

            onCancel: function(data) {
                alert("Pago Cancelado")
                console.log(data);
            }

        }).render('#paypal-button-container');

        // Prevenir comportamiento no deseado de PayPal
        document.addEventListener('click', function(e) {
            if (e.target.closest('#paypal-button-container iframe')) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);
        
    </script>
</body>
</html>