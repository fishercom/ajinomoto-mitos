<?php
/**
 * Plantilla de correo enviado al recibir un mito desde el formulario "Cuéntanos tu mito".
 *
 * Variables esperadas en scope (ver ajinomoto_mitos_get_mail_mito_html() en functions.php):
 * @var string $cabecera_url URL absoluta de la imagen de cabecera.
 * @var string $nombre
 * @var string $dni
 * @var string $email
 * @var string $celular
 * @var string $mensaje_mito
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<html>
<head>
	<meta charset="UTF-8">
	<title>Ajinomoto Mitos</title>
</head>
<body>
	<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%"
		style="max-width: 750px; font-family:Arial, Helvetica, sans-serif;  font-size: 14px; color: #000000;">
		<tr>
			<td>
				<img src="<?php echo esc_url( $cabecera_url ); ?>" alt="ajinomoto mitos" width="100%">
			</td>
		</tr>

		<tr>
			<td align="center" style="padding-top: 40px;">
				<p>Registro de datos del web de Mitos del Sabor.</p>
				<h2 style="font-size: 20px; margin: 40px 0 20px; color: #EA7B00;">Datos personales</h2>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="5" cellspacing="0" width="100%" align="center" style="font-size: 12px; color: #BE4474; max-width: 500px;">
					<tr>
						<td>Nombres y apellidos</td>
						<td>Número de DNI</td>
					</tr>
					<tr>
						<td style="font-size: 14px; color: #000000;"><?php echo esc_html( $nombre ); ?></td>
						<td style="font-size: 14px; color: #000000;"><?php echo esc_html( $dni ); ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>Correo electrónico</td>
						<td>Número de celular</td>
					</tr>
					<tr>
						<td style="font-size: 14px; color: #000000;"><?php echo esc_html( $email ); ?></td>
						<td style="font-size: 14px; color: #000000;"><?php echo esc_html( $celular ); ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 40px 0;">
				<table cellpadding="0" cellspacing="0" width="100%" align="center" style="max-width: 460px;">
					<tr>
						<td style="padding: 40px 20px; font-size: 16px; line-height: 22px; text-align: center;background: #F2F2F2; border-radius: 20px;">
							<h2 style="font-size: 20px; color: #EA7B00;">¿Cuál es tu mito?</h2>
							<p><?php echo nl2br( esc_html( $mensaje_mito ) ); ?></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
