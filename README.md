# CSP
The best way to learn CSP is to play with policy creation & see how policies affect
resources on the page!

When you run locally you have the added benefit of CSP violation reporting being displayed in page.

```sh
# Run with docker ....
docker-compose up
open http://localhost:8100/
```

## Play
- **elements** What elements will load on the page?
- **allow** What should we allow on the page?
- **proof** How can we prove the CSP worked?
  - CSP Reports
  - document.querySelector('iframe').contentWindow.location.href

## Todo
- Setup app for "bad actor" exfil

### Deploy
```sh
heroku create
git subtree push --prefix csp-main heroku master
```

### Links
- https://www.html5rocks.com/en/tutorials/security/content-security-policy/
- https://speakerdeck.com/mikispag/making-csp-great-again-michele-spagnuolo-and-lukas-weichselbaum?slide=9
- https://www.troyhunt.com/understanding-csp-the-video-tutorial-edition/
- http://csp-experiments.appspot.com/strict-dynamic
- http://qnimate.com/content-security-policy-in-nutshell/
- https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
- https://www.telerik.com/blogs/on-cross-site-scripting-and-content-security-policy
- https://dubell.io/exploiting-weak-content-security-policy-csp-rules-for-fun-and-profit/
- https://csp-evaluator.withgoogle.com/
- https://github.com/yandex/csp-tester
- https://content-security-policy.com/
- https://medium.com/kifi-engineering/dont-let-a-content-security-policy-your-extension-s-images-e062d6b88eac
- https://github.com/nico3333fr/CSP-useful/tree/master/csp-for-third-party-services
- https://github.com/w3c/webappsec-csp/issues/116
- https://stripe.com/docs/security
- http://www.cspplayground.com/compliant_examples
- https://www.cspisawesome.com/
- https://github.blog/2013-04-19-content-security-policy/
- https://blog.sendsafely.com/retrofitting-code-for-content-security-policy
- https://erlend.oftedal.no/blog/csp/readiness/
- http://csptesting.herokuapp.com/
- https://github.com/eoftedal/csp-testing
- https://exploited.cz/xss/csp/strict.php
- https://lab.wallarm.com/how-to-trick-csp-in-letting-you-run-whatever-you-want-73cb5ff428aa
- https://blog.innerht.ml/tag/csp/
- https://www.chromestatus.com/feature/5141352765456384