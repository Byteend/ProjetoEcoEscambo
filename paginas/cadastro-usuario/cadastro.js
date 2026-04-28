const email = document.getElementById("email");
const senha = document.getElementById("senha");
const repitaSenha = document.getElementById("repitaSenha");
const botao = document.getElementById("btnCadastrar");
const erro = document.getElementById("erro");

function validar() {
    const emailValido = email.checkValidity();

    const senhaValor = senha.value;

    // regex:
    // mínimo 6 caracteres
    // pelo menos 1 letra minúscula
    // pelo menos 1 maiúscula
    // pelo menos 1 número
    const senhaValida = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/.test(senhaValor);

    const senhasIguais = senha.value === repitaSenha.value && senha.value !== "";

    // Mensagens
    if (!senhaValida) {
        erro.textContent = "Senha deve ter 6+ caracteres, maiúscula, minúscula e número.";
    } else if (!senhasIguais) {
        erro.textContent = "As senhas não coincidem.";
    } else {
        erro.textContent = "";
    }

    // Habilita botão
    if (emailValido && senhaValida && senhasIguais) {
        botao.disabled = false;
    } else {
        botao.disabled = true;
    }
}

// Escuta mudanças nos inputs
email.addEventListener("input", validar);
senha.addEventListener("input", validar);
repitaSenha.addEventListener("input", validar);