$(function() {
    const rot13 = (message) => {
        const alpha = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmABCDEFGHIJKLMNOPQRSTUVWXYZABCDEFGHIJKLM';
        return message.replace(/[a-z]/gi, letter => alpha[alpha.indexOf(letter) + 13]);
    }
    
    let callback = function() {
        setTimeout(function() {
            
            const source = document.getElementById('c747fd04ba117510f399b5f43ba8155087c19523');
            const destination = document.getElementById('ae6b85682663ab4570bd10c67b83d21fe77cdf97');
            
            if (source !== null && destination !== null) {
                
                let id, time;
                
                [id, time] = window.atob(source.value).split('@')
                destination.value = rot13(window.btoa(time + '@' + id));
            }
            
        }, 2500)
    };
    
    callback()
    document.addEventListener('question-about-product-form-loaded', callback);
});