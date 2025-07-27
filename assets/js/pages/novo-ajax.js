		
		var firstRun = true;
    	var inTest = 0
    	var isRun = false
    	var masterList = []
    	var cardsInTest = []
    	var chkinfos = {}
    	const sessions = {}
    	var sessionsLimit = 2;
    	var liveChegandoPruPru = new Audio('/live.mp3')

    	// Select elements
    	const button = document.getElementById('kt_docs_toast_stack_button');
    	const container = document.getElementById('kt_docs_toast_stack_container');
    	const targetElement = document.querySelector('[data-kt-docs-toast="stack"]'); // Use CSS class or HTML attr to avoid duplicating ids

    	// Remove base element markup
    	targetElement.parentNode.removeChild(targetElement);


    	function resetMenu() {
	    	chkinfos = {
	    		checked: 0,
	    		live: 0,
	    		die: 0,
	    		error: 0,
	    		loaded: 0,
	    		limit: 500	    	}

	    	if (chkinfos.limit == 0){
	    		chkinfos.limit = "Infinito";
	    	}
    	}
    	resetMenu()


    	function updateMenu(obj = {}) {
    		Object.entries(obj).forEach(([key, value]) => {
    			chkinfos[key] = value
				});

    		Object.entries(chkinfos).forEach(([key, value]) => {
    			$(`#${key}Count`).html(value)
				});
    	}
    	updateMenu()

    	function containsCard(string){
    		return string.match(/^(?:4[0-9]{12}(?:[0-9]{3})?|(?:5[1-5][0-9]{2}|222[1-9]|22[3-9][0-9]|2[3-6][0-9]{2}|27[01][0-9]|2720)[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|6(?:011|5[0-9]{2})[0-9]{12}|(?:2131|1800|35\d{3})\d{11})\|/)
    	}

    	function setList(){
				$("#listaCHK").val(masterList.join('\n'))
    	}

    	function switchCHK(btn) {
    		objFun = {
    			startCHK: () => {
    				firstRun = true;
    				isRun = true;
    				resetMenu()
						var lista = $("#listaCHK").val()
						masterList = lista.split('\n').filter(containsCard)
						updateMenu({loaded: masterList.length})
						setList()
						if (masterList.length > chkinfos.limit && chkinfos.limit != 0 && chkinfos.limit != "Infinito"){
							updateMenu({error: chkinfos.error+1})
							$(`#errorCHK`).append(`<tr>
									      <td>Erro</td>
									      <td>---</td>
									      <td>Você colocou uma lista muito grande</td>
									    </tr>`)
						} else {
							testLoop()
						}
    			},
    			pauseCHK: () => {
    				if (isRun){
    					isRun = false;
    					$('#pauseCHK').html(`<div class="spinner-border spinner-border-sm" role="status">
									          <span class="sr-only">Carregando...</span>
									        </div>`)
    				}
    			},
    			stopCHK: () => {
    				if (isRun){
	    				isRun = 0;
	    				Object.entries(cardsInTest).forEach(([key, value]) => {
	    					masterList.unshift(key)
	    					value.abort()
							});
							setList()
    				}
    			}
    		}
    		objFun[btn.id]()
    	}

    	function appendResult(obj, current) {
    		delete cardsInTest[current];
				if (obj.success){
	    		liveChegandoPruPru.play();
			    // Create new toast element
			    const newToast = targetElement.cloneNode(true);
			    container.append(newToast);

			    // Create new toast instance --- more info: https://getbootstrap.com/docs/5.1/components/toasts/#getorcreateinstance
			    const toast = bootstrap.Toast.getOrCreateInstance(newToast, {delay: 1300});

			    // Toggle toast to show --- more info: https://getbootstrap.com/docs/5.1/components/toasts/#show
			    toast.show();
					updateMenu({live: chkinfos.live+1, checked: chkinfos.checked+1})
					$(`#liveCHK`).append(`<tr>
							      <td>Aprovada</td>
							      <td>${current}</td>
							      <td>${obj.message}</td>
							    </tr>`)
				} else if(obj.success == false){
					updateMenu({die: chkinfos.die+1, checked: chkinfos.checked+1})
					$(`#dieCHK`).append(`<tr>
							      <td>Reprovada</td>
							      <td>${current}</td>
							      <td>${obj.message}</td>
							    </tr>`)
				} else {
					updateMenu({error: chkinfos.error+1, checked: chkinfos.checked+1})
					$(`#errorCHK`).append(`<tr>
							      <td>Erro</td>
							      <td>${current}</td>
							      <td>${obj.message}</td>
							    </tr>`)
				}
				$(`#${current.replace(/\D/g, "")}`).remove()
				inTest--;
				return testLoop();
    	}

    	async function testLoop() {
    		if (inTest == 0 && !isRun){
    			$("#pauseCHK").html("PAUSAR")
    		}
    		if (inTest < sessionsLimit && masterList.length > 0 && isRun){
    			inTest++
    			// let controller = new AbortController()
    			let current = masterList[0]
    			masterList.shift()
    			setList()

    			$("#runCHK").prepend(`<tr id="${current.replace(/\D/g, "")}">
									      <td>....</td>
									      <td>${current}</td>
									      <td>
									        <div class="spinner-border spinner-border-sm" role="status">
									          <span class="sr-only">Carregando...</span>
									        </div>
									      </td>
									    </tr>`)

    			cardsInTest[current] = $.getJSON( '/api.php?api=itau&lista='+current+(firstRun ? '&verify=true':''), function(obj) {
																	  // console.log( "success" );
																	  firstRun = false;
    																appendResult(obj, current)
																	})
																  .done(function() {
																    // console.log( "second success" );
																  })
																  .fail(function(e) {
    																if (e.statusText == "abort"){
    																	e.responseText = "Cancelado pelo usuário"
    																}
    																appendResult({success: null, message: e.responseText}, current)
																    // console.log( "error" );
																  })
																  .always(function(e) {
																    // console.log( "complete" );
																  });
    			return testLoop();
    		}
    		if (cardsInTest.length == 0 && inTest == 0){
    			isRun = false;
    		}
    	}