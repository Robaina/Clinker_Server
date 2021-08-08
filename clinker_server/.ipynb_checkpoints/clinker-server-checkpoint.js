// Clinker on the Server
// Semidan Robaina Estevez (srobaina@ull.edu.es)

let temp_dir = localStorage.getItem("temp_user_dir");
let iframe = document.getElementById("clinker-web");
let example_content = `
<html>
 <head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="clinker_server/clinker-server.css">
 </head>

 <body>
  <div id="img-container">
   <img id="example-img" src="clinker_server/example.svg">
  </div>
 </body>
</html>`;

// Check if plot.html is ready on the server
function loadPlotWebpage() {
  let xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
         let html_str = updateHTMLtoShowGeneInfoOnHover(xhttp.responseText);
         iframe.srcdoc = html_str;
         iframe.onload = function () {
           // Change max height of Options Box
           iframe.style.display = "inherit";
           let options_box = iframe.contentWindow.document.getElementById("div-floater");
           options_box.style["max-height"] = "70vh";
           options_box.children[0].innerHTML = "Options";
           let load_button = options_box.children[1].children[5].children[0];
           if (load_button !== undefined) {load_button.style.display = "none";}
           // Show Gene Info on Hover
           showGeneLabelOnHover();
         };
      } else {
        iframe.srcdoc = example_content;
      }
  };
  xhttp.open("GET", temp_dir.concat("/plot.html"), true);
  xhttp.send();
}

let help_pressed = true;
function showHelp() {
  let help = document.getElementById("help-text");
  if (help_pressed) {
    help.style.display = "inherit";
    help.scrollIntoView(true);
    document.documentElement.style["overflow-y"] = "inherit";
  } else {
    help.style.display = "none";
    document.documentElement.style["overflow-y"] = "hidden";
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
  }
  help_pressed = !help_pressed;
}

loadPlotWebpage();

// Add protein function to genes
// function addGeneFunctionToLabels(gbk_data) {
//   let geneLabels = iframe.contentWindow.document.getElementsByClassName("geneLabel");
//   for (let label of geneLabels) {
//     let protein_id = label.innerHTML;
//     let protein_function = gbk_data[protein_id];
//     label.innerHTML += "<p>" + protein_function + "</p>";
//     // label.style.display = "inherit";
//   }
// }


// Show Gene Info on Hover
function updateHTMLtoShowGeneInfoOnHover(html_str) {
  console.log("updated");
  let updated_html_str = html_str.replace(
    'on("contextmenu",g.contextMenu)', 'on("mouseover",g.contextMenu)').replace(
      'a.transition().delay(1e3)', 'a.transition().delay(5e6)'
    );
  return updated_html_str
}

function showGeneInfo(div) {
  div.style.opacity = "1";
}

function hideGeneInfo(div) {
  div.style.opacity = "0";
}

function showGeneLabelOnHover() {
  let gene_info_div = iframe.contentWindow.document.getElementsByClassName("tooltip")[0];
  let genes = iframe.contentWindow.document.getElementsByClassName("gene");
  for (gene of genes) {
    gene.onmouseover = function() {
      showGeneInfo(gene_info_div);
    }
    gene.onmouseout = function() {
      hideGeneInfo(gene_info_div);
    }
  }
}

// Hide gene labels input
// function hideGeneLabelsInput() {
//   let inputs = iframe.contentWindow.document.getElementsByClassName("setting");
//   for (let input of inputs) {
//     if(input.textContent.includes("Show gene labels")) {
//       input.style.display = "none";
//     }
//
//   }
// }

// let xhttp_json = new XMLHttpRequest();
// xhttp_json.onreadystatechange = function() {
//   if (this.readyState == 4 && this.status == 200) {
//     let gbk_data = JSON.parse(xhttp_json.responseText);
//     loadPlotWebpage();
//   }
// };
// xhttp_json.open("GET", temp_dir.concat("/gbk_data.json"), true);
// xhttp_json.send();
