'use strict';

onmessage = function(e) {
    console.log('Worker: Message received from main script');
    let result = e.data[0] * e.data[1];
    if (isNaN(result)) {
      postMessage('Please write two numbers');
      return;
    }
    
    console.log('Worker: Posting message back to main script');
    postMessage(result);
};