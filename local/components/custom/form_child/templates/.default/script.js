(function () {
    window.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById('custom_form');
        const validator = new JustValidate(form)
        validator.addField(form.querySelector('input[name=first_name]'), [{ rule: 'required' }])
        validator.addField(form.querySelector('input[name=last_name'), [{ rule: 'required' }])
        validator.addField(form.querySelector('input[name=phone]'), [{ rule: 'required' }, { rule: "number" }])
        validator.onSuccess(async (e) => {
            e.preventDefault()
            const path = form.getAttribute('action')
            const headers = {
                'Content-Type': 'application/json'
            }
            const body = JSON.stringify({
                first_name: form.querySelector('input[name=first_name]').value,
                last_name: form.querySelector('input[name=last_name').value,
                phone: form.querySelector('input[name=phone]').value,
                has_access: form.querySelector('input[name=has_access]').checked,
            })
            const method = "post"
            let data = await fetch(path, { method, headers, body })

            if (data.ok) {
                alert('Запись успешно добавлена!')
            } else {
                alert('Что то пошло не так!')
            }
        })
    })

})()
