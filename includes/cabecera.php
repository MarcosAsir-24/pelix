<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$nombre = $_SESSION['nombre'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;
$rutaBase = $rutaBase ?? '.';
$usuarioConectado = isset($_SESSION['nombre']);

$datos_usuario = null;
if ($usuarioConectado && $usuario_id) {
    $mysqli = new mysqli("localhost", "root", "rootroot", "peliculas");
    if (!$mysqli->connect_errno) {
        $res = $mysqli->query("SELECT * FROM usuarios WHERE id=" . intval($usuario_id) . " LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            $datos_usuario = $row;
        }
    }
}
$foto_perfil = $datos_usuario['foto_perfil'] ?? null;
$foto_path = $foto_perfil ? __DIR__ . '/../img/perfiles/' . $foto_perfil : null;
$foto_url = ($foto_perfil && $foto_path && file_exists($foto_path) && is_file($foto_path))
    ? $rutaBase . '/img/perfiles/' . $foto_perfil . '?v=' . @filemtime($foto_path)
    : $rutaBase . '/img/default-profile.png';
?>
<style>
.header {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 24px;
    background:rgb(0, 0, 0);
    min-height: 70px;
    position: relative;
    z-index: 10;
}
.header .search-bar {
    flex: 1 1 0;
    display: flex;
    justify-content: center;
    align-items: center;
    max-width: 600px;
    position: relative;
    margin: 0 200px;
    z-index: 20;
}
/* Centrar el contenedor de sugerencias justo debajo del buscador */
.sugerencias-autocomplete {
    position: absolute;
    left: 47%;
    transform: translateX(-50%);
    top: 100%;
    width: 535px;
    max-width: 90vw;
    background: #222;
    border: 1px solid #bb860b;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.18);
    display: none;
    z-index: 99;
}
.sugerencias-autocomplete .sugerencia-item {
    display: flex;
    align-items: center;
    padding: 8px 14px;
    color: #FFD54F;
    text-decoration: none;
    border-bottom: 1px solid #333;
    transition: background 0.2s;
}
.sugerencias-autocomplete .sugerencia-item:last-child {
    border-bottom: none;
}
.sugerencias-autocomplete .sugerencia-item:hover {
    background: #333;
    color: #fff;
}
.sugerencias-autocomplete img {
    width: 38px;
    height: 54px;
    object-fit: cover;
    border-radius: 4px;
    margin-right: 12px;
    border: 1px solid #444;
}
.header .auth-links {
    display: flex;
    gap: 10px;
    align-items: center;
}
.header .auth-btn {
    padding: 10px 18px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 1em;
    border: 2px solid #bb860b;
    background: #111;
    color: #bb860b;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
    cursor: pointer;
    display: inline-block;
}
.header .auth-btn:hover {
    background: #bb860b;
    color: #111;
}
.boton, .auth-btn, #cerrarSesionBtn {
    padding: 12px 25px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 1em;
    border: 2px solid #bb860b;
    background: #111;
    color: #bb860b;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
    cursor: pointer;
    display: inline-block;
    margin-top: 10px;
}
.boton:hover, .auth-btn:hover, #cerrarSesionBtn:hover {
    background: #bb860b;
    color: #111;
}
.popup-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.65);
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
}
.popup-overlay[style*="display: flex"] {
    display: flex !important;
}
.popup-contenido {
    background: #181818;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.35);
    padding: 40px 30px 30px 30px;
    max-width: 400px;
    margin: auto;
    position: relative;
    text-align: center;
}
.perfil-foto {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #bb860b;
    margin: 0 auto 18px auto;
    background: #222;
    box-shadow: 0 4px 16px rgba(187,134,11,0.15);
    display: block;
}
.perfil-foto-edit {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 10px;
    width: 100%;
    max-width: 220px;
    margin-left: auto;
    margin-right: auto;
}
.perfil-foto-edit label.boton-archivo {
    margin-top: 8px;
    width: 100%;
    max-width: 220px;
    background: #222;
    color: #bb860b;
    border: 2px solid #bb860b;
    border-radius: 6px;
    font-weight: bold;
    font-size: 1em;
    padding: 12px 0;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    text-align: center;
    display: block;
    box-sizing: border-box;
}
.perfil-foto-edit label.boton-archivo:hover {
    background: #bb860b;
    color: #111;
}
.perfil-foto-edit input[type="file"] {
    display: none;
}
.perfil-foto-edit .boton {
    margin-top: 8px;
    width: 100%;
    max-width: 220px;
    box-sizing: border-box;
}
.perfil-info h2 {
    color: #fff;
    font-size: 1.7em;
    font-weight: bold;
    margin: 0 0 6px 0;
    letter-spacing: 0.5px;
}
.perfil-info .perfil-email {
    color: #bb860b;
    font-size: 1.05em;
    margin-bottom: 18px;
    font-weight: 500;
}
.perfil-info .perfil-dato {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ccc;
    margin: 10px 0;
    font-size: 1em;
    justify-content: left;
}
.perfil-info .perfil-dato i {
    color: #bb860b;
    font-size: 1.1em;
    width: 22px;
    text-align: center;
}
.perfil-info .perfil-suscripcion {
    margin: 18px 0 0 0;
    font-size: 1.1em;
    font-weight: bold;
}
.perfil-info .perfil-suscripcion .premium {
    color: #FFD54F;
}
.perfil-info .perfil-suscripcion .no-premium {
    color: #f00;
}
#cerrarSesionBtn {
    margin-top: 22px;
    width: 100%;
}
.menu-cuenta-slide {
    position: fixed;
    top: 0;
    right: -320px;
    width: 300px;
    height: 100vh;
    background: #181818;
    z-index: 99999;
    transition: right 0.3s ease;
    padding: 20px;
    box-shadow: -5px 0 15px rgba(0,0,0,0.5);
    overflow-y: auto; /* <-- Añadido para evitar desbordamiento */
}

.menu-cuenta-slide.open {
    right: 0;
}

.menu-cuenta-slide .close-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: none;
    border: none;
    color: #bb860b;
    font-size: 24px;
    cursor: pointer;
}

.menu-botones {
    margin-top: 30px;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.cuenta-btn {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: #111;
    color: #FFD54F;
    border: 2px solid #bb860b;
    border-radius: 5px;
    text-decoration: none;
    transition: all 0.3s;
}

.cuenta-btn:hover {
    background: #bb860b;
    color: #111;
}

.cuenta-btn i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}
.header .account-wrapper {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header .logout-btn {
    color: #bb860b;
    font-size: 1.2em;
    cursor: pointer;
    transition: color 0.2s;
}

.header .logout-btn:hover {
    color: #FFD54F;
}
.perfil-mini {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 18px;
}
.perfil-mini img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #FFD54F;
    background: #222;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(187,134,11,0.10);
}
.perfil-mini .nombre {
    color: #FFD54F;
    font-weight: bold;
    font-size: 1.1em;
    margin-bottom: 2px;
    text-align: center;
}
.perfil-mini .email {
    color: #bbb;
    font-size: 0.97em;
    text-align: center;
    word-break: break-all;
}
</style>
<div class="header">
    <a href="<?= $rutaBase ?>/pagina_principal.php" class="logo" tabindex="0">
        <img src="<?= $rutaBase ?>/img/logopequenosinfondo.png" alt="PELIX">
    </a>
    <form class="search-bar" method="GET" action="<?= $rutaBase ?>/buscar.php">
        <input type="text" name="buscar" placeholder="Buscar por título..." autocomplete="off" />
        <button type="submit"><i class="fas fa-search"></i></button>
        <div id="sugerencias" class="sugerencias-autocomplete"></div>
    </form>
    <?php if (!$usuarioConectado): ?>
        <div class="auth-links">
            <a href="<?= $rutaBase ?>/login.php" class="auth-btn">Iniciar sesión</a>
            <a href="<?= $rutaBase ?>/micuenta.php" class="auth-btn">Registrarme</a>
        </div>
    <?php else: ?>
        <div class="account-wrapper">
            <div class="account" tabindex="0" id="btnMiCuenta" style="cursor:pointer;">
                <i class="fas fa-user-circle"></i> Mi cuenta
            </div>
            <i class="fas fa-sign-out-alt logout-btn" onclick="window.location.href='logout.php'" title="Cerrar sesión"></i>
        </div>
    <?php endif; ?>
</div>

<!-- Menú lateral de cuenta -->
<div id="menu-cuenta-slide" class="menu-cuenta-slide">
    <button class="close-btn" onclick="cerrarMenuCuenta()">&times;</button>
    <?php if ($usuarioConectado && $datos_usuario): ?>
        <div class="perfil-mini">
            <img src="<?= htmlspecialchars($foto_url) ?>" alt="">
            <div class="nombre"><?= htmlspecialchars($datos_usuario['nombre']) ?></div>
            <div class="email"><?= htmlspecialchars($datos_usuario['email']) ?></div>
        </div>
        <div style="margin-top: 40px">
            <a href="#" class="cuenta-btn" id="btnVerPerfil">
                <i class="fas fa-user"></i> Mi Perfil
            </a>
            <a href="favoritos.php" class="cuenta-btn">
                <i class="fas fa-heart"></i> Mis Películas
            </a>
            <a href="logout.php" class="cuenta-btn">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>  
    <?php endif; ?>
</div>

<!-- Popup Mi Perfil -->
<div id="popup-perfil" class="popup-overlay" style="display:none;">
    <div class="popup-contenido" style="max-width:420px;">
        <button class="close-btn" onclick="cerrarPopupPerfil()" style="position:absolute;top:10px;right:16px;font-size:22px;background:none;border:none;color:#FFD54F;cursor:pointer;">&times;</button>
        <form id="form-foto-perfil" method="POST" enctype="multipart/form-data" action="subir_foto_perfil.php">
            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>">
            <div class="perfil-foto-edit">
                <img id="img-perfil-preview" class="perfil-foto" src="<?= htmlspecialchars($foto_url) ?>" alt="">
                <label class="boton-archivo" tabindex="0">
                    Cambiar foto
                    <input type="file" name="foto_perfil" id="input-foto-perfil" accept="image/*" onchange="previewFotoPerfil(this)">
                </label>
                <button type="submit" class="boton" id="btn-guardar-foto" style="display:none;">Guardar Foto</button>
            </div>
        </form>
        <div class="perfil-info">
            <h2><?= htmlspecialchars($datos_usuario['nombre']) . ' ' . htmlspecialchars($datos_usuario['apellidos']) ?></h2>
            <div class="perfil-email"><?= htmlspecialchars($datos_usuario['email']) ?></div>
            <div class="perfil-dato"><i class="fas fa-phone"></i> <?= htmlspecialchars($datos_usuario['telefono']) ?></div>
            <div class="perfil-dato"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($datos_usuario['direccion']) ?></div>
            <div class="perfil-dato"><i class="fas fa-birthday-cake"></i> <?= htmlspecialchars($datos_usuario['fecha_nacimiento']) ?></div>
            <div class="perfil-suscripcion">
                <?php if (!empty($datos_usuario['premium']) && $datos_usuario['premium']): ?>
                    <span class="premium">Suscripción: Premium</span>
                <?php else: ?>
                    <span class="no-premium">Suscripción: Básica</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmar Cierre de Sesión -->
<div id="modalConfirmarCerrarSesion" class="popup-overlay" style="display: none;" onclick="cerrarConfirmacionLogout()">
    <div class="popup-contenido" onclick="event.stopPropagation();">
        <p>¿Estás seguro de que quieres cerrar sesión?</p>
        <button onclick="confirmarLogout(true)" class="boton">Sí</button>
        <button onclick="confirmarLogout(false)" class="boton">No</button>
    </div>
</div>

<script>
function mostrarPopup() {
    document.getElementById('popup-suscripcion').style.display = 'flex';
}
function cerrarPopup() {
    document.getElementById('popup-suscripcion').style.display = 'none';
}
function cerrarConfirmacionLogout() {
    document.getElementById("modalConfirmarCerrarSesion").style.display = "none";
}

function abrirMenuCuenta() {
    document.getElementById('menu-cuenta-slide').classList.add('open');
    document.body.style.overflow = 'hidden'; // Evita el scroll del fondo
}

function cerrarMenuCuenta() {
    document.getElementById('menu-cuenta-slide').classList.remove('open');
    document.body.style.overflow = ''; // Restaura el scroll
}

// Evento para cerrar al hacer clic fuera
document.addEventListener('click', function(e) {
    const menu = document.getElementById('menu-cuenta-slide');
    const btnCuenta = document.getElementById('btnMiCuenta');
    
    if (!menu.contains(e.target) && e.target !== btnCuenta && !btnCuenta.contains(e.target)) {
        cerrarMenuCuenta();
    }
});

// Evento para cerrar con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarMenuCuenta();
    }
});


document.addEventListener('DOMContentLoaded', function () {
    var btnMiCuenta = document.getElementById('btnMiCuenta');
    if (btnMiCuenta) {
        btnMiCuenta.onclick = function(e) {
            e.preventDefault();
            abrirMenuCuenta();
        };
    }

    const inputBusqueda = document.querySelector('input[name="buscar"]');
    const sugerenciasDiv = document.getElementById('sugerencias');

    if (inputBusqueda && sugerenciasDiv) {
        inputBusqueda.addEventListener('input', function () {
            const query = this.value;
            if (query.length === 0) {
                sugerenciasDiv.innerHTML = '';
                sugerenciasDiv.style.display = 'none';
                return;
            }
            fetch('buscar_sugerencias.php?buscar=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(function(data) {
                    sugerenciasDiv.innerHTML = '';
                    if (data.length === 0) {
                        sugerenciasDiv.style.display = 'none';
                        return;
                    }
                    data.forEach(function(pelicula) {
                        const item = document.createElement('a');
                        item.href = '/TFG/peliculas/pelicula.php?pelicula=' + pelicula.id;
                        item.classList.add('sugerencia-item');
                        item.innerHTML = `
                            <img src="/TFG/${pelicula.imagen}" alt="${pelicula.titulo}">
                            <span>${pelicula.titulo}</span>
                        `;
                        sugerenciasDiv.appendChild(item);
                    });
                    sugerenciasDiv.style.display = 'block';
                })
                .catch(function(error) {
                    console.error('Error fetching suggestions:', error);
                    sugerenciasDiv.style.display = 'none';
                });
        });

        document.addEventListener('click', function (e) {
            if (!sugerenciasDiv.contains(e.target) && e.target !== inputBusqueda) {
                sugerenciasDiv.style.display = 'none';
            }
        });
    }

    // Botón cerrar sesión
    const btnCerrarSesion = document.getElementById("cerrarSesionBtn");
    if (btnCerrarSesion) {
        btnCerrarSesion.addEventListener("click", function (e) {
            e.preventDefault();
            document.getElementById("modalConfirmarCerrarSesion").style.display = "flex";
        });
    }

    // Accesibilidad: permite activar el input file con Enter/Espacio en el label
    const labelArchivo = document.querySelector('.boton-archivo');
    if (labelArchivo) {
        labelArchivo.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                document.getElementById('input-foto-perfil').click();
            }
        });
    }

    // Mostrar los botones solo si el menú está abierto al cargar
    var menu = document.getElementById('menu-cuenta-slide');
    if (menu && menu.classList.contains('open')) {
        var botones = document.querySelectorAll('#cuenta-botones .cuenta-btn');
        botones.forEach(function(btn) { btn.style.display = 'flex'; });
    }

    // Botón "Mi Perfil" muestra el popup
    var btnVerPerfil = document.getElementById('btnVerPerfil');
    if (btnVerPerfil) {
        btnVerPerfil.onclick = function(e) {
            e.preventDefault();
            cerrarMenuCuenta();
            // Mostrar el popup de perfil correctamente
            var popupPerfil = document.getElementById('popup-perfil');
            if (popupPerfil) {
                popupPerfil.style.display = 'flex';
                popupPerfil.style.alignItems = 'center';
                popupPerfil.style.justifyContent = 'center';
            }
        };
    }
});

function confirmarLogout(confirmado) {
    document.getElementById("modalConfirmarCerrarSesion").style.display = "none";
    if (confirmado) {
        window.location.href = "logout.php";
    }
}

function cerrarPopupPerfil() {
    var popupPerfil = document.getElementById('popup-perfil');
    if (popupPerfil) popupPerfil.style.display = 'none';
}

// Preview de la foto de perfil y mostrar botón guardar
function previewFotoPerfil(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('img-perfil-preview').src = e.target.result;
            document.getElementById('btn-guardar-foto').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === "Escape") cerrarMenuCuenta();
});
document.addEventListener('click', function(e) {
    var menu = document.getElementById('menu-cuenta-slide');
    if (menu && menu.classList.contains('open') && !menu.contains(e.target) && !e.target.classList.contains('account')) {
        cerrarMenuCuenta();
    }
});
</script>
