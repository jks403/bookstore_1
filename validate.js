function validation() 
{
	//create a new variable, "isbn"
	// 'document' references the webpage.
	// 'forms' references all forms in the webpage.
	// the first box [] references the unique name of the form
	// the second box [] references the key
	// '.value' references the specific value we want from that key
	var isbn = document.forms["drop_down"]["isbn"].value;
	if(isbn == "")
	{
		
		alert("Please select a title.");
		return false;
	}
}
