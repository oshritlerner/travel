const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

const country = 'italy';

const start_date = '2014-02-17';
const last_date = '2014-08-17';

function pad(n, width, z) {
	z = z || '0';
	n = n + '';
	return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function formatted_date(date)
{
	return pad(date.getDate(), 2) +'-' + pad((date.getMonth() + 1), 2) + '-' + date.getFullYear();
}

function link_date(date)
{
	return '\"'+pad(date.getDate(), 2) +'\", \"' + pad((date.getMonth() + 1), 2) + '\", \"' + date.getFullYear()+'\"';
}

function year_month_name(date)
{
	return monthNames[date.getMonth()] + ' ' + date.getFullYear();
}


function menu()
{
	let menu_arr = ["<div class='main'><br><a href='javascript:void(0)' onclick='main(0);  return false;'>Main</a>"];
	let date = new Date(start_date);
	let current_year_month = '';
	while (Date.parse(date)<=Date.parse(last_date))
	{

		if (current_year_month !== year_month_name(date))
		{
			current_year_month = year_month_name(date);
			menu_arr.push("</div>");
			menu_arr.push("<div class='main'>");
			menu_arr.push("<a href='javascript:void(0)' onclick='$(\"#"+pad((date.getMonth()+1), 2)+"\").toggle();'>"+current_year_month+"</a>");
			menu_arr.push("</div>");
			menu_arr.push("<div style='display:none;' id='"+pad((date.getMonth()+1), 2)+"'>");
		}
		menu_arr.push("<br><br><a href='javascript:void(0)' onclick='view("+link_date(date)+");'>"+formatted_date(date)+"</a>");
		date.setDate(date.getDate() + 1);
	}
	menu_arr.push("</div>");

	$("#menu").html(menu_arr.join('\n'));
	$("#loading-indicator").hide();
}



function view(day, month, year)
{
	let date_arr = [];
	$("#loading-indicator").show();
	date_arr.push('<h3>'+ day + '-' + month +'-' + year+'</h3>');
	let current_date = new Date(year+'-'+month+'-'+day);
	let next_date = new Date(year+'-'+month+'-'+day);
	let prev_date = new Date(year+'-'+month+'-'+day);

	fetch('txts/'+month+day+'.txt')
		.then(function(response) {
			response.text().then(function(text) {
				text = text.replace(/(?:\r\n|\r|\n)/g, '<br>');
				date_arr.push('<div style="text-align: center">'+text+'</div>');

				all_images[month][day].map(img=>
					date_arr.push("<div class='main' style='text-align: center; padding:10px;'><img src='"+img+"'></div>")
				);
				date_arr.push("<div class='main' style=\"text-align: center; padding:10px;\">");

				if (Date.parse(current_date)<Date.parse(last_date)){
					next_date.setDate(current_date.getDate() + 1);
					date_arr.push("<button onclick ='view("+link_date(next_date)+");' class='btn btn-info icon-white'></i> next</button>");
				}


				if (Date.parse(current_date)>Date.parse(start_date)){
					prev_date.setDate(current_date.getDate()  -1);
					date_arr.push("<button onclick ='view("+link_date(prev_date)+");' class='btn btn-info icon-white'></i> previous</button>");
				}

				date_arr.push("</div>");


				$("#view").html(date_arr.join('\n'));
				$("#loading-indicator").hide();
				$("html, body").animate({ scrollTop: 0 }, "slow");

			});
		});
}

function main()
{
	fetch('main.txt')
		.then(function(response) {
			response.text().then(function(text) {
				text = text.replace(/(?:\r\n|\r|\n)/g, '<br>');

				$("#view").html('<div style="text-align: center">'+text+'</div>');
				$("#loading-indicator").hide();
			});
		});
}

