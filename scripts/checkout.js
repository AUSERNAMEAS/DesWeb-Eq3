document.addEventListener('DOMContentLoaded',()=>{
    const cardOption = document.getElementById('card-option');
        const cardDetails = document.getElementById('card-details');

        // Muestra u oculta los detalles de la tarjeta según la opción seleccionada
        document.querySelectorAll('input[name="payment-method"]').forEach(radio => {
            radio.addEventListener('change', () => {
                if (cardOption.checked) {
                    cardDetails.style.display = 'block';
                    cardDetails.querySelectorAll('input').forEach(input => input.required = true);
                } else {
                    cardDetails.style.display = 'none';
                    cardDetails.querySelectorAll('input').forEach(input => input.required = false);
                }
            });
        });

document.getElementById("pay-btn").addEventListener('click',()=>{
    alert('se ha realizado el pago correctamente');
    window.location.href = 'FakeShop.html';
})
})

