// Manejo del formulario "Cuéntanos tu mito"
document.addEventListener('DOMContentLoaded', () => {
    const formMito = document.getElementById('form-cuentanos-mito');
    const msgDiv = document.getElementById('form-message');

    if (formMito) {
        // Enlazar el enlace de envío <a> para que actúe como disparador del submit
        const linkSubmit = formMito.querySelector('.btn-submit-form') || formMito.querySelector('.btn');
        if (linkSubmit) {
            linkSubmit.addEventListener('click', (e) => {
                e.preventDefault();
                if (linkSubmit.classList.contains('disabled')) return;
                // Disparar el evento submit en el formulario
                formMito.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
            });
        }

        formMito.addEventListener('submit', (e) => {
            e.preventDefault();

            // Ocultar mensaje previo
            if (msgDiv) {
                msgDiv.style.display = 'none';
                msgDiv.className = '';
                msgDiv.textContent = '';
            }

            // Validar checkboxes
            const chkDatos = document.getElementById('datos');
            const chkTerminos = document.getElementById('terminos');
            
            const dniVal = document.getElementById('dni').value.trim();
            const celularVal = document.getElementById('celular').value.trim();
            const emailVal = document.getElementById('email').value.trim();

            if (!/^[0-9]+$/.test(dniVal)) {
                showMsg('El Número de DNI debe contener solo números.', 'error');
                return;
            }

            if (!/^[0-9]+$/.test(celularVal)) {
                showMsg('El Número de celular debe contener solo números.', 'error');
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailVal)) {
                showMsg('Por favor, ingresa un correo electrónico válido.', 'error');
                return;
            }

            if (chkDatos && !chkDatos.checked) {
                showMsg('Debes autorizar el tratamiento de tus datos personales.', 'error');
                return;
            }
            if (chkTerminos && !chkTerminos.checked) {
                showMsg('Debes aceptar los Términos y Condiciones.', 'error');
                return;
            }

            // Validar reCAPTCHA
            if (typeof grecaptcha !== 'undefined') {
                const recaptchaVal = grecaptcha.getResponse();
                if (recaptchaVal.length === 0) {
                    showMsg('Por favor, marca la casilla "No soy un robot" de reCAPTCHA.', 'error');
                    return;
                }
            } else {
                showMsg('Error cargando el verificador reCAPTCHA. Por favor, recarga la página.', 'error');
                return;
            }

            // Deshabilitar botón de envío
            if (linkSubmit) {
                linkSubmit.classList.add('disabled');
                linkSubmit.textContent = 'Enviando...';
                linkSubmit.style.pointerEvents = 'none';
            }

            // Preparar datos
            const formData = new FormData(formMito);

            // Enviar AJAX
            const ajaxUrl = typeof ajinomoto_params !== 'undefined' ? ajinomoto_params.ajax_url : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showMsg(data.data.message, 'success');
                    formMito.reset();
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.reset();
                    }
                } else {
                    showMsg(data.data.message || 'Ocurrió un error al enviar el mito.', 'error');
                }
            })
            .catch(err => {
                console.error(err);
                showMsg('Error de red. Por favor, inténtalo de nuevo más tarde.', 'error');
            })
            .finally(() => {
                if (linkSubmit) {
                    linkSubmit.classList.remove('disabled');
                    linkSubmit.textContent = 'Envía mito';
                    linkSubmit.style.pointerEvents = 'auto';
                }
            });
        });
    }

    function showMsg(text, type) {
        if (!msgDiv) {
            alert(text);
            return;
        }
        msgDiv.textContent = text;
        msgDiv.style.display = 'block';
        if (type === 'success') {
            msgDiv.style.backgroundColor = '#e6ffed';
            msgDiv.style.color = '#22863a';
            msgDiv.style.border = '1px solid #34d058';
        } else {
            msgDiv.style.backgroundColor = '#ffeef0';
            msgDiv.style.color = '#cb2431';
            msgDiv.style.border = '1px solid #f97583';
        }
    }
});
