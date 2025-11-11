<script src="https://cdnjs.cloudflare.com/ajax/libs/tesseract.js/4.1.1/tesseract.min.js"></script>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-bd lobidrag">
            <div class="panel-heading">
                <div class="panel-title">
                    <h4>Upload Invoice</h4>
                </div>
            </div>
            <?php echo form_open_multipart('parseinvoice/parseinvoice/upload_invoice', array('class' => 'form-vertical', 'id' => 'upload_invoice')) ?>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Emirates Id Front</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                              <!--<input class="form-control" type="file" id="pdf" name="pdf" placeholder="Select a PDF file" required=""> -->
                                <input type="file" id="select" accept="image/png, image/gif, image/webp, image/jpeg">
                        
                                <textarea id="result"></textarea>
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Emirates Id No</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                                <input class="form-control" type="text" name="emirates_id" id="emirates_id" value="" />
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Name</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                                <input class="form-control" type="text" name="name" id="name" value="" />
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Dob</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                                <input class="form-control" type="text" name="dob" id="dob" value="" />
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Nationality</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                                <input class="form-control" type="text" name="nationality" id="nationality" value="" />
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                    <div class="col-sm-6">
                      <div class="form-group row">
                         <label for="pdf" class="col-sm-4 col-form-label">Emirates Id Back</label>
                         <div class="col-sm-8">
                            <div class="pdf-input">
                              <!--<input class="form-control" type="file" id="pdf" name="pdf" placeholder="Select a PDF file" required=""> -->
                                <input type="file" id="select2" accept="image/png, image/gif, image/webp, image/jpeg">
                        
                                <textarea id="result2"></textarea>
                           </div> 
                           <!--<input type="submit" name="submit" class="btn btn-large" value="Submit"> -->
                        </div>
                      </div>
                    </div>
                </div>
                <br>
                <div class="form-group row text-right">
                    <div class="col-sm-12 p-20">
                        <input type="submit" id="upload_invoice_submit" class="btn btn-success" name="upload_invoice_submit"
                            value="<?php echo display('submit') ?>" tabindex="17" />

                    </div>
                </div>
            </div>
            <?php echo form_close() ?>
        </div>
    </div>
</div>
<script>

window.addEventListener("load", async () => {
  // (A) GET HTML ELEMENTS
  const hSel = document.getElementById("select"),
        hRes = document.getElementById("result"),
        emirates_id = document.getElementById("emirates_id"),
        name = document.getElementById("name"),
        dob = document.getElementById("dob"),
        nationality = document.getElementById("nationality"),
        hSel2 = document.getElementById("select2"),
        hRes2 = document.getElementById("result2");
 
  // (C) ON FILE SELECT - IMAGE TO TEXT
  hSel.onchange = async () => {
    const worker = await Tesseract.createWorker();
    await worker.loadLanguage("eng");
    await worker.initialize("eng");

    // (C) RESULT
      worker.recognize(hSel.files[0])
        .then(result => {
            var stringToReplace = result.data.text;
            var desired = stringToReplace.replace(/[`~!@#$%^&*()_|+\=?'",.<>\{\}\[\]\\]/gi, '&');
            hRes.value = desired;
            
            
            
            var emirates_idString = desired.match(new RegExp("784-" + "(.*)" + " &"));
            emirates_id.value = "784-" + emirates_idString[1];
            
            var nameString = desired.match(new RegExp("Name: " + "(.*)" + " :"));
            name.value = nameString[1];
            
            var dobString = desired.match(new RegExp("Date of Birth: " + "(.*)" + " "));
            dob.value = dobString[1];
            
            // var emirates_idString = desired.match("784-(.*) ");
            // emirates_id.value = "784-" + emirates_idString[1];
            
            // var nameString = desired.match("Name: (.*) :");
            // name.value = nameString[1];
            
            // var dobString = desired.match("Date of Birth: (.*) ");
            // dob.value = dobString[1];
            
            // var nationalityString = desired.match("Nationality: (.*) ");
            // nationality.value = nationalityString[1];
        })
  };
//   hSel2.onchange = async () => {
//     const worker = await Tesseract.createWorker();
//     await worker.loadLanguage("eng");
//     await worker.initialize("eng");

//     // (C) RESULT
//       worker.recognize(hSel2.files[0])
//         .then(result => {
//             var stringToReplace = result.data.text;
//             var desired = stringToReplace.replace(/[`~!@#$%^&*()_|+\=?'",.<>\{\}\[\]\\]/gi, '&');
//             hRes.value = desired;
            
//             var emirates_idString = desired.match("784-(.*) &");
//             emirates_id.value = "784-" + emirates_idString[1];
            
//             var nameString = desired.match("Name: (.*) :");
//             name.value = nameString[1];
            
//             var dobString = desired.match("Date of Birth: (.*) ");
//             dob.value = dobString[1];
//         })
//   };
  
  
//   // (A) GET HTML ELEMENTS
//   const hSel = document.getElementById("select"),
//         hRes = document.getElementById("result"),
//         emirates_id = document.getElementById("emirates_id"),
//         name = document.getElementById("name"),
//         dob = document.getElementById("dob"),
//         nationality = document.getElementById("nationality"),
//         hSel2 = document.getElementById("select2"),
//         hRes2 = document.getElementById("result2");
//         occupation = document.getElementById("occupation"),
//         employer = document.getElementById("employer"),
//         issuing_place = document.getElementById("issuing_place");
//         // Abu Dhabi, Ajman, Dubai, Fujairah, Ras Al Khaimah, Sharjah and Umm Al Quwain
//  var place;
//   // (C) ON FILE SELECT - IMAGE TO TEXT
//   hSel.onchange = async () => {
//     const worker = await Tesseract.createWorker();
//     await worker.loadLanguage("eng");
//     await worker.initialize("eng");

//     // (C) RESULT
//       worker.recognize(hSel.files[0])
//         .then(result => {
//             var stringToReplace = result.data.text;
//             var desired = stringToReplace.replace(/[^A-Za-z0-9,.:/-\s]/g, "&&");
//             desired = desired.replace(/(\r\n|\n|\r)/gm, "**");
//             // var desired = stringToReplace.replace(/[`~!@#$%^&*()_|+\=?'",.<>\{\}\[\]\\]/gi, '&');
//             hRes.value = desired;
            
            
//             // var emirates_idString = desired.match(new RegExp("784-" + "(.*)" + " &"));
//             // emirates_id.value = "784-" + emirates_idString[1];
            
//             var emirates_idString = desired.match("784-(.*) &&");
//             emirates_id.value = "784-" + emirates_idString[1];
            
//             var nameString = desired.match("Name: (.*) :**");
//             name.value = nameString[1];
            
//             var dobString = desired.match("Date of Birth: (.*)**");
//             dob.value = dobString[1];
            
//             var nationalityString = desired.match("Nationality: (.*)**");
//             nationality.value = nationalityString[1];
//         })
//   };
//   hSel2.onchange = async () => {
//     const worker = await Tesseract.createWorker();
//     await worker.loadLanguage("eng");
//     await worker.initialize("eng");

//     // (C) RESULT
//       worker.recognize(hSel2.files[0])
//         .then(result => {
//             var stringToReplace = result.data.text;
//             var desired = stringToReplace.replace(/[^A-Za-z0-9,.:/ -]/g, "&&");
//             hRes2.value = desired;
            
//             var issuing_placeString = desired.match("Place:(.+)&");
            
//             if(issuing_placeString[1].includes("Abu Dhabi")){
//                 place="Abu Dhabi";
//             }
//             else if(issuing_placeString[1].includes("Dubai")){
//                 place="Dubai";
//             }
//             else if(issuing_placeString[1].includes("Ajman")){
//                 place="Ajman";
//             }
//             else if(issuing_placeString[1].includes("Fujairah")){
//                 place="Fujairah";
//             }
//             else if(issuing_placeString[1].includes("Ras Al Khaimah")){
//                 place="Ras Al Khaimah";
//             }
//             else if(issuing_placeString[1].includes("Sharjah")){
//                 place="Sharjah";
//             }
//             else if(issuing_placeString[1].includes("Umm Al Quwain")){
//                 place="Umm Al Quwain";
//             }
            
//             issuing_place.value = place;
            
//             var occupationString = desired.match("Occupation: (.*)&&");
//             occupation.value = occupationString[1];
            
//             var employerString = desired.match("Employer:(.*)&&");
//             employer.value = employerString[1];
//         })
//   };
});
</script>