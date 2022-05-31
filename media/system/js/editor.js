// Crispage Editor script
const emodes = {};
var toggle;
const editId = (new URLSearchParams(document.location.search)).get("edit_id");

function switchEditor(editor, parent) {
	if (emodes[editor.id]) {
		toggle.innerHTML = "HTML Editor";
		setCookie("editor_" + editId, "cke", 365);
		ClassicEditor.create(editor).catch(e => console.error);
	} else {
		toggle.innerHTML = "WYSIWYG Editor";
		setCookie("editor_" + editId, "text", 365)
		parent.querySelector(".ck-editor__editable").ckeditorInstance.destroy();
	}
}

window.onload = function() {
	const editors = document.querySelectorAll(".editor");

	editors.forEach(function(editor) {
		emodes[editor.id] = false;
		const parent = editor.parentNode;

		editor.style.width = "100%";
		editor.style.height = "300px";
		editor.style.fontFamily = "monospace";
		editor.autocomplete = "off";

		editor.onkeydown = function() {
			if (event.keyCode === 9) {
				var v = this.value,
					s = this.selectionStart,
					e = this.selectionEnd;
				this.value = v.substring(0, s)+ "\t" + v.substring(e);
				this.selectionStart = this.selectionEnd = s + 1;
				return false;
			}
		}

		toggle = document.createElement("button");
		toggle.type = "button";
		toggle.className = "btn btn-primary";
		toggle.innerHTML = "WYSIWYG Editor";

		toggle.onclick = function() {
			emodes[editor.id] = !emodes[editor.id];
			switchEditor(editor, parent);
		}

		if (getCookie("editor_" + editId) == "cke") {
			emodes[editor.id] = true;
			switchEditor(editor, parent);
		}

		parent.insertBefore(toggle, editor);
	});
}
