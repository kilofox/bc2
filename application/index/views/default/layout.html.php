<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		<meta name="description" content="Metro, a sleek, intuitive, and powerful framework for faster and easier web development for Windows Metro Style."/>
		<meta name="keywords" content="HTML, CSS, JS, JavaScript, framework, metro, front-end, frontend, web development"/>
		<meta name="author" content="Sergey Pimenov and Metro UI CSS contributors"/>
		<link rel="shortcut icon" type="image/x-icon" href="<?php echo $baseUrl; ?>/favicon.ico"/>
		<title>News portal :: Metro UI CSS - The front-end framework for developing projects on the web in Windows Metro Style</title>
		<script src="<?php echo $baseUrl; ?>/assets/default/js/jquery.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/assets/default/js/jquery.dropotron.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/assets/default/js/jquery.scrollex.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/assets/default/js/skel.min.js"></script>
		<script src="<?php echo $baseUrl; ?>/assets/default/js/util.js"></script>
		<script src="<?php echo $baseUrl; ?>/assets/default/js/respond.min.js"></script>
		<style>
			@import url(http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css);
			@import url("https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300italic,600,600italic");
			html,body,div,span,applet,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,a,abbr,acronym,address,big,cite,code,del,dfn,em,img,ins,kbd,q,s,samp,small,strike,strong,sub,sup,tt,var,b,u,i,center,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,embed,figure,figcaption,footer,header,hgroup,menu,nav,output,ruby,section,summary,time,mark,audio,video {
				margin:0;
				padding:0;
				border:0;
				font-size:100%;
				font:inherit;
				vertical-align:baseline;
			}
			article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section {
				display:block;
			}
			body {
				line-height:1;
			}
			ol,ul {
				list-style:none;
			}
			blockquote,q {
				quotes:none;
			}
			blockquote:before,blockquote:after,q:before,q:after {
				content:'';
				content:none;
			}
			table {
				border-collapse:collapse;
				border-spacing:0;
			}
			body {
				-webkit-text-size-adjust:none;
			}
			*,*:before,*:after {
				-moz-box-sizing:border-box;
				-webkit-box-sizing:border-box;
				box-sizing:border-box;
			}
			.row {
				border-bottom:solid 1px transparent;
				-moz-box-sizing:border-box;
				-webkit-box-sizing:border-box;
				box-sizing:border-box;
			}
			.row > * {
				float:left;
				-moz-box-sizing:border-box;
				-webkit-box-sizing:border-box;
				box-sizing:border-box;
			}
			.row:after,.row:before {
				content:'';
				display:block;
				clear:both;
				height:0;
			}
			.row.uniform > * >:first-child {
				margin-top:0;
			}
			.row.uniform > * >:last-child {
				margin-bottom:0;
			}
			.row.\30 \25 > * {
				padding:0 0 0 0em;
			}
			.row.\30 \25 {
				margin:0 0 -1px 0em;
			}
			.row.uniform.\30 \25 > * {
				padding:0em 0 0 0em;
			}
			.row.uniform.\30 \25 {
				margin:0em 0 -1px 0em;
			}
			.row > * {
				padding:0 0 0 1.35em;
			}
			.row {
				margin:0 0 -1px -1.35em;
			}
			.row.uniform > * {
				padding:1.35em 0 0 1.35em;
			}
			.row.uniform {
				margin:-1.35em 0 -1px -1.35em;
			}
			.row.\32 00\25 > * {
				padding:0 0 0 2.7em;
			}
			.row.\32 00\25 {
				margin:0 0 -1px -2.7em;
			}
			.row.uniform.\32 00\25 > * {
				padding:2.7em 0 0 2.7em;
			}
			.row.uniform.\32 00\25 {
				margin:-2.7em 0 -1px -2.7em;
			}
			.row.\31 50\25 > * {
				padding:0 0 0 2.025em;
			}
			.row.\31 50\25 {
				margin:0 0 -1px -2.025em;
			}
			.row.uniform.\31 50\25 > * {
				padding:2.025em 0 0 2.025em;
			}
			.row.uniform.\31 50\25 {
				margin:-2.025em 0 -1px -2.025em;
			}
			.row.\35 0\25 > * {
				padding:0 0 0 0.675em;
			}
			.row.\35 0\25 {
				margin:0 0 -1px -0.675em;
			}
			.row.uniform.\35 0\25 > * {
				padding:0.675em 0 0 0.675em;
			}
			.row.uniform.\35 0\25 {
				margin:-0.675em 0 -1px -0.675em;
			}
			.row.\32 5\25 > * {
				padding:0 0 0 0.3375em;
			}
			.row.\32 5\25 {
				margin:0 0 -1px -0.3375em;
			}
			.row.uniform.\32 5\25 > * {
				padding:0.3375em 0 0 0.3375em;
			}
			.row.uniform.\32 5\25 {
				margin:-0.3375em 0 -1px -0.3375em;
			}
			.\31 2u,.\31 2u\24 {
				width:100%;
				clear:none;
				margin-left:0;
			}
			.\31 1u,.\31 1u\24 {
				width:91.6666666667%;
				clear:none;
				margin-left:0;
			}
			.\31 0u,.\31 0u\24 {
				width:83.3333333333%;
				clear:none;
				margin-left:0;
			}
			.\39 u,.\39 u\24 {
				width:75%;
				clear:none;
				margin-left:0;
			}
			.\38 u,.\38 u\24 {
				width:66.6666666667%;
				clear:none;
				margin-left:0;
			}
			.\37 u,.\37 u\24 {
				width:58.3333333333%;
				clear:none;
				margin-left:0;
			}
			.\36 u,.\36 u\24 {
				width:50%;
				clear:none;
				margin-left:0;
			}
			.\35 u,.\35 u\24 {
				width:41.6666666667%;
				clear:none;
				margin-left:0;
			}
			.\34 u,.\34 u\24 {
				width:33.3333333333%;
				clear:none;
				margin-left:0;
			}
			.\33 u,.\33 u\24 {
				width:25%;
				clear:none;
				margin-left:0;
			}
			.\32 u,.\32 u\24 {
				width:16.6666666667%;
				clear:none;
				margin-left:0;
			}
			.\31 u,.\31 u\24 {
				width:8.3333333333%;
				clear:none;
				margin-left:0;
			}
			.\31 2u\24 + *,.\31 1u\24 + *,.\31 0u\24 + *,.\39 u\24 + *,.\38 u\24 + *,.\37 u\24 + *,.\36 u\24 + *,.\35 u\24 + *,.\34 u\24 + *,.\33 u\24 + *,.\32 u\24 + *,.\31 u\24 + * {
				clear:left;
			}
			.\-11u {
				margin-left:91.66667%;
			}
			.\-10u {
				margin-left:83.33333%;
			}
			.\-9u {
				margin-left:75%;
			}
			.\-8u {
				margin-left:66.66667%;
			}
			.\-7u {
				margin-left:58.33333%;
			}
			.\-6u {
				margin-left:50%;
			}
			.\-5u {
				margin-left:41.66667%;
			}
			.\-4u {
				margin-left:33.33333%;
			}
			.\-3u {
				margin-left:25%;
			}
			.\-2u {
				margin-left:16.66667%;
			}
			.\-1u {
				margin-left:8.33333%;
			}
			@media screen and (max-width:1680px) {
				.row > * {
					padding:0 0 0 1.35em;
				}
				.row {
					margin:0 0 -1px -1.35em;
				}
				.row.uniform > * {
					padding:1.35em 0 0 1.35em;
				}
				.row.uniform {
					margin:-1.35em 0 -1px -1.35em;
				}
				.row.\32 00\25 > * {
					padding:0 0 0 2.7em;
				}
				.row.\32 00\25 {
					margin:0 0 -1px -2.7em;
				}
				.row.uniform.\32 00\25 > * {
					padding:2.7em 0 0 2.7em;
				}
				.row.uniform.\32 00\25 {
					margin:-2.7em 0 -1px -2.7em;
				}
				.row.\31 50\25 > * {
					padding:0 0 0 2.025em;
				}
				.row.\31 50\25 {
					margin:0 0 -1px -2.025em;
				}
				.row.uniform.\31 50\25 > * {
					padding:2.025em 0 0 2.025em;
				}
				.row.uniform.\31 50\25 {
					margin:-2.025em 0 -1px -2.025em;
				}
				.row.\35 0\25 > * {
					padding:0 0 0 0.675em;
				}
				.row.\35 0\25 {
					margin:0 0 -1px -0.675em;
				}
				.row.uniform.\35 0\25 > * {
					padding:0.675em 0 0 0.675em;
				}
				.row.uniform.\35 0\25 {
					margin:-0.675em 0 -1px -0.675em;
				}
				.row.\32 5\25 > * {
					padding:0 0 0 0.3375em;
				}
				.row.\32 5\25 {
					margin:0 0 -1px -0.3375em;
				}
				.row.uniform.\32 5\25 > * {
					padding:0.3375em 0 0 0.3375em;
				}
				.row.uniform.\32 5\25 {
					margin:-0.3375em 0 -1px -0.3375em;
				}
				.\31 2u\28xlarge\29,.\31 2u\24\28xlarge\29 {
					width:100%;
					clear:none;
					margin-left:0;
				}
				.\31 1u\28xlarge\29,.\31 1u\24\28xlarge\29 {
					width:91.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 0u\28xlarge\29,.\31 0u\24\28xlarge\29 {
					width:83.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\39 u\28xlarge\29,.\39 u\24\28xlarge\29 {
					width:75%;
					clear:none;
					margin-left:0;
				}
				.\38 u\28xlarge\29,.\38 u\24\28xlarge\29 {
					width:66.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\37 u\28xlarge\29,.\37 u\24\28xlarge\29 {
					width:58.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\36 u\28xlarge\29,.\36 u\24\28xlarge\29 {
					width:50%;
					clear:none;
					margin-left:0;
				}
				.\35 u\28xlarge\29,.\35 u\24\28xlarge\29 {
					width:41.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\34 u\28xlarge\29,.\34 u\24\28xlarge\29 {
					width:33.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\33 u\28xlarge\29,.\33 u\24\28xlarge\29 {
					width:25%;
					clear:none;
					margin-left:0;
				}
				.\32 u\28xlarge\29,.\32 u\24\28xlarge\29 {
					width:16.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 u\28xlarge\29,.\31 u\24\28xlarge\29 {
					width:8.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\31 2u\24\28xlarge\29 + *,.\31 1u\24\28xlarge\29 + *,.\31 0u\24\28xlarge\29 + *,.\39 u\24\28xlarge\29 + *,.\38 u\24\28xlarge\29 + *,.\37 u\24\28xlarge\29 + *,.\36 u\24\28xlarge\29 + *,.\35 u\24\28xlarge\29 + *,.\34 u\24\28xlarge\29 + *,.\33 u\24\28xlarge\29 + *,.\32 u\24\28xlarge\29 + *,.\31 u\24\28xlarge\29 + * {
					clear:left;
				}
				.\-11u\28xlarge\29 {
					margin-left:91.66667%;
				}
				.\-10u\28xlarge\29 {
					margin-left:83.33333%;
				}
				.\-9u\28xlarge\29 {
					margin-left:75%;
				}
				.\-8u\28xlarge\29 {
					margin-left:66.66667%;
				}
				.\-7u\28xlarge\29 {
					margin-left:58.33333%;
				}
				.\-6u\28xlarge\29 {
					margin-left:50%;
				}
				.\-5u\28xlarge\29 {
					margin-left:41.66667%;
				}
				.\-4u\28xlarge\29 {
					margin-left:33.33333%;
				}
				.\-3u\28xlarge\29 {
					margin-left:25%;
				}
				.\-2u\28xlarge\29 {
					margin-left:16.66667%;
				}
				.\-1u\28xlarge\29 {
					margin-left:8.33333%;
				}
			}@media screen and (max-width:1280px) {
				.row > * {
					padding:0 0 0 1.35em;
				}
				.row {
					margin:0 0 -1px -1.35em;
				}
				.row.uniform > * {
					padding:1.35em 0 0 1.35em;
				}
				.row.uniform {
					margin:-1.35em 0 -1px -1.35em;
				}
				.row.\32 00\25 > * {
					padding:0 0 0 2.7em;
				}
				.row.\32 00\25 {
					margin:0 0 -1px -2.7em;
				}
				.row.uniform.\32 00\25 > * {
					padding:2.7em 0 0 2.7em;
				}
				.row.uniform.\32 00\25 {
					margin:-2.7em 0 -1px -2.7em;
				}
				.row.\31 50\25 > * {
					padding:0 0 0 2.025em;
				}
				.row.\31 50\25 {
					margin:0 0 -1px -2.025em;
				}
				.row.uniform.\31 50\25 > * {
					padding:2.025em 0 0 2.025em;
				}
				.row.uniform.\31 50\25 {
					margin:-2.025em 0 -1px -2.025em;
				}
				.row.\35 0\25 > * {
					padding:0 0 0 0.675em;
				}
				.row.\35 0\25 {
					margin:0 0 -1px -0.675em;
				}
				.row.uniform.\35 0\25 > * {
					padding:0.675em 0 0 0.675em;
				}
				.row.uniform.\35 0\25 {
					margin:-0.675em 0 -1px -0.675em;
				}
				.row.\32 5\25 > * {
					padding:0 0 0 0.3375em;
				}
				.row.\32 5\25 {
					margin:0 0 -1px -0.3375em;
				}
				.row.uniform.\32 5\25 > * {
					padding:0.3375em 0 0 0.3375em;
				}
				.row.uniform.\32 5\25 {
					margin:-0.3375em 0 -1px -0.3375em;
				}
				.\31 2u\28large\29,.\31 2u\24\28large\29 {
					width:100%;
					clear:none;
					margin-left:0;
				}
				.\31 1u\28large\29,.\31 1u\24\28large\29 {
					width:91.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 0u\28large\29,.\31 0u\24\28large\29 {
					width:83.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\39 u\28large\29,.\39 u\24\28large\29 {
					width:75%;
					clear:none;
					margin-left:0;
				}
				.\38 u\28large\29,.\38 u\24\28large\29 {
					width:66.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\37 u\28large\29,.\37 u\24\28large\29 {
					width:58.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\36 u\28large\29,.\36 u\24\28large\29 {
					width:50%;
					clear:none;
					margin-left:0;
				}
				.\35 u\28large\29,.\35 u\24\28large\29 {
					width:41.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\34 u\28large\29,.\34 u\24\28large\29 {
					width:33.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\33 u\28large\29,.\33 u\24\28large\29 {
					width:25%;
					clear:none;
					margin-left:0;
				}
				.\32 u\28large\29,.\32 u\24\28large\29 {
					width:16.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 u\28large\29,.\31 u\24\28large\29 {
					width:8.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\31 2u\24\28large\29 + *,.\31 1u\24\28large\29 + *,.\31 0u\24\28large\29 + *,.\39 u\24\28large\29 + *,.\38 u\24\28large\29 + *,.\37 u\24\28large\29 + *,.\36 u\24\28large\29 + *,.\35 u\24\28large\29 + *,.\34 u\24\28large\29 + *,.\33 u\24\28large\29 + *,.\32 u\24\28large\29 + *,.\31 u\24\28large\29 + * {
					clear:left;
				}
				.\-11u\28large\29 {
					margin-left:91.66667%;
				}
				.\-10u\28large\29 {
					margin-left:83.33333%;
				}
				.\-9u\28large\29 {
					margin-left:75%;
				}
				.\-8u\28large\29 {
					margin-left:66.66667%;
				}
				.\-7u\28large\29 {
					margin-left:58.33333%;
				}
				.\-6u\28large\29 {
					margin-left:50%;
				}
				.\-5u\28large\29 {
					margin-left:41.66667%;
				}
				.\-4u\28large\29 {
					margin-left:33.33333%;
				}
				.\-3u\28large\29 {
					margin-left:25%;
				}
				.\-2u\28large\29 {
					margin-left:16.66667%;
				}
				.\-1u\28large\29 {
					margin-left:8.33333%;
				}
			}@media screen and (max-width:980px) {
				.row > * {
					padding:0 0 0 1.35em;
				}
				.row {
					margin:0 0 -1px -1.35em;
				}
				.row.uniform > * {
					padding:1.35em 0 0 1.35em;
				}
				.row.uniform {
					margin:-1.35em 0 -1px -1.35em;
				}
				.row.\32 00\25 > * {
					padding:0 0 0 2.7em;
				}
				.row.\32 00\25 {
					margin:0 0 -1px -2.7em;
				}
				.row.uniform.\32 00\25 > * {
					padding:2.7em 0 0 2.7em;
				}
				.row.uniform.\32 00\25 {
					margin:-2.7em 0 -1px -2.7em;
				}
				.row.\31 50\25 > * {
					padding:0 0 0 2.025em;
				}
				.row.\31 50\25 {
					margin:0 0 -1px -2.025em;
				}
				.row.uniform.\31 50\25 > * {
					padding:2.025em 0 0 2.025em;
				}
				.row.uniform.\31 50\25 {
					margin:-2.025em 0 -1px -2.025em;
				}
				.row.\35 0\25 > * {
					padding:0 0 0 0.675em;
				}
				.row.\35 0\25 {
					margin:0 0 -1px -0.675em;
				}
				.row.uniform.\35 0\25 > * {
					padding:0.675em 0 0 0.675em;
				}
				.row.uniform.\35 0\25 {
					margin:-0.675em 0 -1px -0.675em;
				}
				.row.\32 5\25 > * {
					padding:0 0 0 0.3375em;
				}
				.row.\32 5\25 {
					margin:0 0 -1px -0.3375em;
				}
				.row.uniform.\32 5\25 > * {
					padding:0.3375em 0 0 0.3375em;
				}
				.row.uniform.\32 5\25 {
					margin:-0.3375em 0 -1px -0.3375em;
				}
				.\31 2u\28medium\29,.\31 2u\24\28medium\29 {
					width:100%;
					clear:none;
					margin-left:0;
				}
				.\31 1u\28medium\29,.\31 1u\24\28medium\29 {
					width:91.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 0u\28medium\29,.\31 0u\24\28medium\29 {
					width:83.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\39 u\28medium\29,.\39 u\24\28medium\29 {
					width:75%;
					clear:none;
					margin-left:0;
				}
				.\38 u\28medium\29,.\38 u\24\28medium\29 {
					width:66.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\37 u\28medium\29,.\37 u\24\28medium\29 {
					width:58.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\36 u\28medium\29,.\36 u\24\28medium\29 {
					width:50%;
					clear:none;
					margin-left:0;
				}
				.\35 u\28medium\29,.\35 u\24\28medium\29 {
					width:41.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\34 u\28medium\29,.\34 u\24\28medium\29 {
					width:33.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\33 u\28medium\29,.\33 u\24\28medium\29 {
					width:25%;
					clear:none;
					margin-left:0;
				}
				.\32 u\28medium\29,.\32 u\24\28medium\29 {
					width:16.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 u\28medium\29,.\31 u\24\28medium\29 {
					width:8.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\31 2u\24\28medium\29 + *,.\31 1u\24\28medium\29 + *,.\31 0u\24\28medium\29 + *,.\39 u\24\28medium\29 + *,.\38 u\24\28medium\29 + *,.\37 u\24\28medium\29 + *,.\36 u\24\28medium\29 + *,.\35 u\24\28medium\29 + *,.\34 u\24\28medium\29 + *,.\33 u\24\28medium\29 + *,.\32 u\24\28medium\29 + *,.\31 u\24\28medium\29 + * {
					clear:left;
				}
				.\-11u\28medium\29 {
					margin-left:91.66667%;
				}
				.\-10u\28medium\29 {
					margin-left:83.33333%;
				}
				.\-9u\28medium\29 {
					margin-left:75%;
				}
				.\-8u\28medium\29 {
					margin-left:66.66667%;
				}
				.\-7u\28medium\29 {
					margin-left:58.33333%;
				}
				.\-6u\28medium\29 {
					margin-left:50%;
				}
				.\-5u\28medium\29 {
					margin-left:41.66667%;
				}
				.\-4u\28medium\29 {
					margin-left:33.33333%;
				}
				.\-3u\28medium\29 {
					margin-left:25%;
				}
				.\-2u\28medium\29 {
					margin-left:16.66667%;
				}
				.\-1u\28medium\29 {
					margin-left:8.33333%;
				}
			}@media screen and (max-width:736px) {
				.row > * {
					padding:0 0 0 1.35em;
				}
				.row {
					margin:0 0 -1px -1.35em;
				}
				.row.uniform > * {
					padding:1.35em 0 0 1.35em;
				}
				.row.uniform {
					margin:-1.35em 0 -1px -1.35em;
				}
				.row.\32 00\25 > * {
					padding:0 0 0 2.7em;
				}
				.row.\32 00\25 {
					margin:0 0 -1px -2.7em;
				}
				.row.uniform.\32 00\25 > * {
					padding:2.7em 0 0 2.7em;
				}
				.row.uniform.\32 00\25 {
					margin:-2.7em 0 -1px -2.7em;
				}
				.row.\31 50\25 > * {
					padding:0 0 0 2.025em;
				}
				.row.\31 50\25 {
					margin:0 0 -1px -2.025em;
				}
				.row.uniform.\31 50\25 > * {
					padding:2.025em 0 0 2.025em;
				}
				.row.uniform.\31 50\25 {
					margin:-2.025em 0 -1px -2.025em;
				}
				.row.\35 0\25 > * {
					padding:0 0 0 0.675em;
				}
				.row.\35 0\25 {
					margin:0 0 -1px -0.675em;
				}
				.row.uniform.\35 0\25 > * {
					padding:0.675em 0 0 0.675em;
				}
				.row.uniform.\35 0\25 {
					margin:-0.675em 0 -1px -0.675em;
				}
				.row.\32 5\25 > * {
					padding:0 0 0 0.3375em;
				}
				.row.\32 5\25 {
					margin:0 0 -1px -0.3375em;
				}
				.row.uniform.\32 5\25 > * {
					padding:0.3375em 0 0 0.3375em;
				}
				.row.uniform.\32 5\25 {
					margin:-0.3375em 0 -1px -0.3375em;
				}
				.\31 2u\28small\29,.\31 2u\24\28small\29 {
					width:100%;
					clear:none;
					margin-left:0;
				}
				.\31 1u\28small\29,.\31 1u\24\28small\29 {
					width:91.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 0u\28small\29,.\31 0u\24\28small\29 {
					width:83.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\39 u\28small\29,.\39 u\24\28small\29 {
					width:75%;
					clear:none;
					margin-left:0;
				}
				.\38 u\28small\29,.\38 u\24\28small\29 {
					width:66.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\37 u\28small\29,.\37 u\24\28small\29 {
					width:58.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\36 u\28small\29,.\36 u\24\28small\29 {
					width:50%;
					clear:none;
					margin-left:0;
				}
				.\35 u\28small\29,.\35 u\24\28small\29 {
					width:41.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\34 u\28small\29,.\34 u\24\28small\29 {
					width:33.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\33 u\28small\29,.\33 u\24\28small\29 {
					width:25%;
					clear:none;
					margin-left:0;
				}
				.\32 u\28small\29,.\32 u\24\28small\29 {
					width:16.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 u\28small\29,.\31 u\24\28small\29 {
					width:8.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\31 2u\24\28small\29 + *,.\31 1u\24\28small\29 + *,.\31 0u\24\28small\29 + *,.\39 u\24\28small\29 + *,.\38 u\24\28small\29 + *,.\37 u\24\28small\29 + *,.\36 u\24\28small\29 + *,.\35 u\24\28small\29 + *,.\34 u\24\28small\29 + *,.\33 u\24\28small\29 + *,.\32 u\24\28small\29 + *,.\31 u\24\28small\29 + * {
					clear:left;
				}
				.\-11u\28small\29 {
					margin-left:91.66667%;
				}
				.\-10u\28small\29 {
					margin-left:83.33333%;
				}
				.\-9u\28small\29 {
					margin-left:75%;
				}
				.\-8u\28small\29 {
					margin-left:66.66667%;
				}
				.\-7u\28small\29 {
					margin-left:58.33333%;
				}
				.\-6u\28small\29 {
					margin-left:50%;
				}
				.\-5u\28small\29 {
					margin-left:41.66667%;
				}
				.\-4u\28small\29 {
					margin-left:33.33333%;
				}
				.\-3u\28small\29 {
					margin-left:25%;
				}
				.\-2u\28small\29 {
					margin-left:16.66667%;
				}
				.\-1u\28small\29 {
					margin-left:8.33333%;
				}
			}@media screen and (max-width:480px) {
				.row > * {
					padding:0 0 0 1.35em;
				}
				.row {
					margin:0 0 -1px -1.35em;
				}
				.row.uniform > * {
					padding:1.35em 0 0 1.35em;
				}
				.row.uniform {
					margin:-1.35em 0 -1px -1.35em;
				}
				.row.\32 00\25 > * {
					padding:0 0 0 2.7em;
				}
				.row.\32 00\25 {
					margin:0 0 -1px -2.7em;
				}
				.row.uniform.\32 00\25 > * {
					padding:2.7em 0 0 2.7em;
				}
				.row.uniform.\32 00\25 {
					margin:-2.7em 0 -1px -2.7em;
				}
				.row.\31 50\25 > * {
					padding:0 0 0 2.025em;
				}
				.row.\31 50\25 {
					margin:0 0 -1px -2.025em;
				}
				.row.uniform.\31 50\25 > * {
					padding:2.025em 0 0 2.025em;
				}
				.row.uniform.\31 50\25 {
					margin:-2.025em 0 -1px -2.025em;
				}
				.row.\35 0\25 > * {
					padding:0 0 0 0.675em;
				}
				.row.\35 0\25 {
					margin:0 0 -1px -0.675em;
				}
				.row.uniform.\35 0\25 > * {
					padding:0.675em 0 0 0.675em;
				}
				.row.uniform.\35 0\25 {
					margin:-0.675em 0 -1px -0.675em;
				}
				.row.\32 5\25 > * {
					padding:0 0 0 0.3375em;
				}
				.row.\32 5\25 {
					margin:0 0 -1px -0.3375em;
				}
				.row.uniform.\32 5\25 > * {
					padding:0.3375em 0 0 0.3375em;
				}
				.row.uniform.\32 5\25 {
					margin:-0.3375em 0 -1px -0.3375em;
				}
				.\31 2u\28xsmall\29,.\31 2u\24\28xsmall\29 {
					width:100%;
					clear:none;
					margin-left:0;
				}
				.\31 1u\28xsmall\29,.\31 1u\24\28xsmall\29 {
					width:91.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 0u\28xsmall\29,.\31 0u\24\28xsmall\29 {
					width:83.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\39 u\28xsmall\29,.\39 u\24\28xsmall\29 {
					width:75%;
					clear:none;
					margin-left:0;
				}
				.\38 u\28xsmall\29,.\38 u\24\28xsmall\29 {
					width:66.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\37 u\28xsmall\29,.\37 u\24\28xsmall\29 {
					width:58.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\36 u\28xsmall\29,.\36 u\24\28xsmall\29 {
					width:50%;
					clear:none;
					margin-left:0;
				}
				.\35 u\28xsmall\29,.\35 u\24\28xsmall\29 {
					width:41.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\34 u\28xsmall\29,.\34 u\24\28xsmall\29 {
					width:33.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\33 u\28xsmall\29,.\33 u\24\28xsmall\29 {
					width:25%;
					clear:none;
					margin-left:0;
				}
				.\32 u\28xsmall\29,.\32 u\24\28xsmall\29 {
					width:16.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 u\28xsmall\29,.\31 u\24\28xsmall\29 {
					width:8.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\31 2u\24\28xsmall\29 + *,.\31 1u\24\28xsmall\29 + *,.\31 0u\24\28xsmall\29 + *,.\39 u\24\28xsmall\29 + *,.\38 u\24\28xsmall\29 + *,.\37 u\24\28xsmall\29 + *,.\36 u\24\28xsmall\29 + *,.\35 u\24\28xsmall\29 + *,.\34 u\24\28xsmall\29 + *,.\33 u\24\28xsmall\29 + *,.\32 u\24\28xsmall\29 + *,.\31 u\24\28xsmall\29 + * {
					clear:left;
				}
				.\-11u\28xsmall\29 {
					margin-left:91.66667%;
				}
				.\-10u\28xsmall\29 {
					margin-left:83.33333%;
				}
				.\-9u\28xsmall\29 {
					margin-left:75%;
				}
				.\-8u\28xsmall\29 {
					margin-left:66.66667%;
				}
				.\-7u\28xsmall\29 {
					margin-left:58.33333%;
				}
				.\-6u\28xsmall\29 {
					margin-left:50%;
				}
				.\-5u\28xsmall\29 {
					margin-left:41.66667%;
				}
				.\-4u\28xsmall\29 {
					margin-left:33.33333%;
				}
				.\-3u\28xsmall\29 {
					margin-left:25%;
				}
				.\-2u\28xsmall\29 {
					margin-left:16.66667%;
				}
				.\-1u\28xsmall\29 {
					margin-left:8.33333%;
				}
			}@media screen and (max-width:360px) {
				.row > * {
					padding:0 0 0 1.35em;
				}
				.row {
					margin:0 0 -1px -1.35em;
				}
				.row.uniform > * {
					padding:1.35em 0 0 1.35em;
				}
				.row.uniform {
					margin:-1.35em 0 -1px -1.35em;
				}
				.row.\32 00\25 > * {
					padding:0 0 0 2.7em;
				}
				.row.\32 00\25 {
					margin:0 0 -1px -2.7em;
				}
				.row.uniform.\32 00\25 > * {
					padding:2.7em 0 0 2.7em;
				}
				.row.uniform.\32 00\25 {
					margin:-2.7em 0 -1px -2.7em;
				}
				.row.\31 50\25 > * {
					padding:0 0 0 2.025em;
				}
				.row.\31 50\25 {
					margin:0 0 -1px -2.025em;
				}
				.row.uniform.\31 50\25 > * {
					padding:2.025em 0 0 2.025em;
				}
				.row.uniform.\31 50\25 {
					margin:-2.025em 0 -1px -2.025em;
				}
				.row.\35 0\25 > * {
					padding:0 0 0 0.675em;
				}
				.row.\35 0\25 {
					margin:0 0 -1px -0.675em;
				}
				.row.uniform.\35 0\25 > * {
					padding:0.675em 0 0 0.675em;
				}
				.row.uniform.\35 0\25 {
					margin:-0.675em 0 -1px -0.675em;
				}
				.row.\32 5\25 > * {
					padding:0 0 0 0.3375em;
				}
				.row.\32 5\25 {
					margin:0 0 -1px -0.3375em;
				}
				.row.uniform.\32 5\25 > * {
					padding:0.3375em 0 0 0.3375em;
				}
				.row.uniform.\32 5\25 {
					margin:-0.3375em 0 -1px -0.3375em;
				}
				.\31 2u\28xxsmall\29,.\31 2u\24\28xxsmall\29 {
					width:100%;
					clear:none;
					margin-left:0;
				}
				.\31 1u\28xxsmall\29,.\31 1u\24\28xxsmall\29 {
					width:91.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 0u\28xxsmall\29,.\31 0u\24\28xxsmall\29 {
					width:83.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\39 u\28xxsmall\29,.\39 u\24\28xxsmall\29 {
					width:75%;
					clear:none;
					margin-left:0;
				}
				.\38 u\28xxsmall\29,.\38 u\24\28xxsmall\29 {
					width:66.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\37 u\28xxsmall\29,.\37 u\24\28xxsmall\29 {
					width:58.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\36 u\28xxsmall\29,.\36 u\24\28xxsmall\29 {
					width:50%;
					clear:none;
					margin-left:0;
				}
				.\35 u\28xxsmall\29,.\35 u\24\28xxsmall\29 {
					width:41.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\34 u\28xxsmall\29,.\34 u\24\28xxsmall\29 {
					width:33.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\33 u\28xxsmall\29,.\33 u\24\28xxsmall\29 {
					width:25%;
					clear:none;
					margin-left:0;
				}
				.\32 u\28xxsmall\29,.\32 u\24\28xxsmall\29 {
					width:16.6666666667%;
					clear:none;
					margin-left:0;
				}
				.\31 u\28xxsmall\29,.\31 u\24\28xxsmall\29 {
					width:8.3333333333%;
					clear:none;
					margin-left:0;
				}
				.\31 2u\24\28xxsmall\29 + *,.\31 1u\24\28xxsmall\29 + *,.\31 0u\24\28xxsmall\29 + *,.\39 u\24\28xxsmall\29 + *,.\38 u\24\28xxsmall\29 + *,.\37 u\24\28xxsmall\29 + *,.\36 u\24\28xxsmall\29 + *,.\35 u\24\28xxsmall\29 + *,.\34 u\24\28xxsmall\29 + *,.\33 u\24\28xxsmall\29 + *,.\32 u\24\28xxsmall\29 + *,.\31 u\24\28xxsmall\29 + * {
					clear:left;
				}
				.\-11u\28xxsmall\29 {
					margin-left:91.66667%;
				}
				.\-10u\28xxsmall\29 {
					margin-left:83.33333%;
				}
				.\-9u\28xxsmall\29 {
					margin-left:75%;
				}
				.\-8u\28xxsmall\29 {
					margin-left:66.66667%;
				}
				.\-7u\28xxsmall\29 {
					margin-left:58.33333%;
				}
				.\-6u\28xxsmall\29 {
					margin-left:50%;
				}
				.\-5u\28xxsmall\29 {
					margin-left:41.66667%;
				}
				.\-4u\28xxsmall\29 {
					margin-left:33.33333%;
				}
				.\-3u\28xxsmall\29 {
					margin-left:25%;
				}
				.\-2u\28xxsmall\29 {
					margin-left:16.66667%;
				}
				.\-1u\28xxsmall\29 {
					margin-left:8.33333%;
				}
			}@-ms-viewport {
				width:device-width;
			}
			body {
				-ms-overflow-style:scrollbar;
			}
			@media screen and (max-width:480px) {
				html,body {
					min-width:320px;
				}
			}body {
				background:#ffffff;
			}
			body.is-loading *,body.is-loading *:before,body.is-loading *:after {
				-moz-animation:none !important;
				-webkit-animation:none !important;
				-ms-animation:none !important;
				animation:none !important;
				-moz-transition:none !important;
				-webkit-transition:none !important;
				-ms-transition:none !important;
				transition:none !important;
			}
			body {
				background-color:#f7f7f7;
				color:#6e6e6e;
			}
			body,input,select,textarea {
				font-family:"Source Sans Pro",Helvetica,sans-serif;
				font-size:14pt;
				font-weight:300;
				line-height:1.65;
				letter-spacing:0.05em;
			}
			@media screen and (max-width:1680px) {
				body,input,select,textarea {
					font-size:12pt;
				}
			}@media screen and (max-width:736px) {
				body,input,select,textarea {
					font-size:11.5pt;
				}
			}@media screen and (max-width:360px) {
				body,input,select,textarea {
					font-size:10.5pt;
				}
			}a {
				-moz-transition:color 0.2s ease-in-out,border-bottom-color 0.2s ease-in-out,background-color 0.2s ease-in-out;
				-webkit-transition:color 0.2s ease-in-out,border-bottom-color 0.2s ease-in-out,background-color 0.2s ease-in-out;
				-ms-transition:color 0.2s ease-in-out,border-bottom-color 0.2s ease-in-out,background-color 0.2s ease-in-out;
				transition:color 0.2s ease-in-out,border-bottom-color 0.2s ease-in-out,background-color 0.2s ease-in-out;
				text-decoration:none;
				border-bottom:dotted 1px;
				color:inherit;
			}
			a:hover {
				border-bottom-color:transparent;
			}
			a:hover strong {
				color:inherit !important;
			}
			strong,b {
				font-weight:600;
			}
			em,i {
				font-style:italic;
			}
			p {
				margin:0 0 2em 0;
			}
			h1,h2,h3,h4,h5,h6 {
				font-weight:300;
				line-height:1.5;
				margin:0 0 0.7em 0;
			}
			h1 a,h2 a,h3 a,h4 a,h5 a,h6 a {
				color:inherit;
				text-decoration:none;
			}
			h1 {
				font-size:3em;
			}
			h2 {
				font-size:2em;
			}
			h3 {
				font-size:1.5em;
			}
			h4 {
				font-size:1.1em;
			}
			h5 {
				font-size:0.9em;
			}
			h6 {
				font-size:0.7em;
			}
			@media screen and (max-width:1280px) {
				h1 {
					font-size:2.5em;
				}
			}@media screen and (max-width:736px) {
				h1 {
					font-size:1.75em;
				}
				h2 {
					font-size:1.5em;
				}
				h3 {
					font-size:1.35em;
				}
				h4 {
					font-size:1em;
				}
				h1 br,h2 br,h3 br,h4 br,h5 br,h6 br {
					display:none;
				}
			}@media screen and (max-width:480px) {
				h2 {
					font-size:1.35em;
				}
				h3 {
					font-size:1em;
				}
			}sub {
				font-size:0.8em;
				position:relative;
				top:0.5em;
			}
			sup {
				font-size:0.8em;
				position:relative;
				top:-0.5em;
			}
			blockquote {
				border-left:solid 4px;
				font-style:italic;
				margin:0 0 2em 0;
				padding:0.5em 0 0.5em 2em;
			}
			code {
				border-radius:4px;
				border:solid 1px;
				font-family:"Courier New",monospace;
				font-size:0.9em;
				margin:0 0.25em;
				padding:0.25em 0.65em;
			}
			pre {
				-webkit-overflow-scrolling:touch;
				font-family:"Courier New",monospace;
				font-size:0.9em;
				margin:0 0 2em 0;
			}
			pre code {
				display:block;
				line-height:1.75;
				padding:1em 1.5em;
				overflow-x:auto;
			}
			hr {
				border:0;
				border-bottom:solid 1px;
				margin:2em 0;
			}
			hr.major {
				margin:3em 0;
			}
			.align-left {
				text-align:left;
			}
			.align-center {
				text-align:center;
			}
			.align-right {
				text-align:right;
			}
			input,select,textarea {
				color:#5e5e5e;
			}
			a:hover {
				color:#72bee1 !important;
			}
			strong,b {
				color:#5e5e5e;
			}
			h1,h2,h3,h4,h5,h6 {
				color:#5e5e5e;
			}
			blockquote {
				border-left-color:#e3e3e3;
			}
			code {
				background:rgba(144,144,144,0.075);
				border-color:#e3e3e3;
			}
			hr {
				border-bottom-color:#e3e3e3;
			}
			.box {
				border-radius:4px;
				border:solid 1px;
				margin-bottom:2em;
				padding:1.5em;
			}
			.box >:last-child,.box >:last-child >:last-child,.box >:last-child >:last-child >:last-child {
				margin-bottom:0;
			}
			.box.alt {
				border:0;
				border-radius:0;
				padding:0;
			}
			.box {
				border-color:#e3e3e3;
			}
			input[type="submit"],input[type="reset"],input[type="button"],button,.button {
				-moz-appearance:none;
				-webkit-appearance:none;
				-ms-appearance:none;
				appearance:none;
				-moz-transition:background-color 0.2s ease-in-out,color 0.2s ease-in-out;
				-webkit-transition:background-color 0.2s ease-in-out,color 0.2s ease-in-out;
				-ms-transition:background-color 0.2s ease-in-out,color 0.2s ease-in-out;
				transition:background-color 0.2s ease-in-out,color 0.2s ease-in-out;
				border-radius:4px;
				border:0;
				cursor:pointer;
				display:inline-block;
				font-weight:600;
				height:3.025em;
				line-height:3.025em;
				padding:0 3em;
				text-align:center;
				text-decoration:none;
				white-space:nowrap;
			}
			input[type="submit"].icon:before,input[type="reset"].icon:before,input[type="button"].icon:before,button.icon:before,.button.icon:before {
				margin-right:0.5em;
				margin-left:-0.35em;
			}
			input[type="submit"].fit,input[type="reset"].fit,input[type="button"].fit,button.fit,.button.fit {
				display:block;
				margin:0 0 1em 0;
				width:100%;
			}
			input[type="submit"].small,input[type="reset"].small,input[type="button"].small,button.small,.button.small {
				font-size:0.8em;
			}
			input[type="submit"].big,input[type="reset"].big,input[type="button"].big,button.big,.button.big {
				font-size:1.35em;
			}
			input[type="submit"].disabled,input[type="submit"]:disabled,input[type="reset"].disabled,input[type="reset"]:disabled,input[type="button"].disabled,input[type="button"]:disabled,button.disabled,button:disabled,.button.disabled,.button:disabled {
				-moz-pointer-events:none;
				-webkit-pointer-events:none;
				-ms-pointer-events:none;
				pointer-events:none;
				opacity:0.35;
			}
			@media screen and (max-width:480px) {
				input[type="submit"],input[type="reset"],input[type="button"],button,.button {
					padding:0;
				}
			}input[type="submit"],input[type="reset"],input[type="button"],button,.button {
				background-color:transparent;
				box-shadow:inset 0 0 0 1px #e3e3e3;
				color:#5e5e5e !important;
			}
			input[type="submit"]:hover,input[type="reset"]:hover,input[type="button"]:hover,button:hover,.button:hover {
				background-color:rgba(144,144,144,0.075);
				color:#5e5e5e !important;
			}
			input[type="submit"]:active,input[type="reset"]:active,input[type="button"]:active,button:active,.button:active {
				background-color:rgba(144,144,144,0.125);
			}
			input[type="submit"].special,input[type="reset"].special,input[type="button"].special,button.special,.button.special {
				box-shadow:none;
				background-color:#72bee1;
				color:#ffffff !important;
			}
			input[type="submit"].special:hover,input[type="reset"].special:hover,input[type="button"].special:hover,button.special:hover,.button.special:hover {
				background-color:#87c8e5;
			}
			input[type="submit"].special:active,input[type="reset"].special:active,input[type="button"].special:active,button.special:active,.button.special:active {
				background-color:#5db4dd;
			}
			form {
				margin:0 0 2em 0;
			}
			label {
				display:block;
				font-size:0.9em;
				font-weight:300;
				margin:0 0 1em 0;
			}
			input[type="text"],input[type="password"],input[type="email"],select,textarea {
				-moz-appearance:none;
				-webkit-appearance:none;
				-ms-appearance:none;
				appearance:none;
				border-radius:4px;
				border:none;
				border:solid 1px;
				color:inherit;
				display:block;
				outline:0;
				padding:0 1em;
				text-decoration:none;
				width:100%;
			}
			input[type="text"]:invalid,input[type="password"]:invalid,input[type="email"]:invalid,select:invalid,textarea:invalid {
				box-shadow:none;
			}
			.select-wrapper {
				text-decoration:none;
				display:block;
				position:relative;
			}
			.select-wrapper:before {
				-moz-osx-font-smoothing:grayscale;
				-webkit-font-smoothing:antialiased;
				font-family:FontAwesome;
				font-style:normal;
				font-weight:normal;
				text-transform:none !important;
			}
			.select-wrapper:before {
				content:'\f078';
				display:block;
				height:2.75em;
				line-height:2.75em;
				pointer-events:none;
				position:absolute;
				right:0;
				text-align:center;
				top:0;
				width:2.75em;
			}
			.select-wrapper select::-ms-expand {
				display:none;
			}
			input[type="text"],input[type="password"],input[type="email"],select {
				height:2.75em;
			}
			textarea {
				padding:0.75em 1em;
			}
			input[type="checkbox"],input[type="radio"] {
				-moz-appearance:none;
				-webkit-appearance:none;
				-ms-appearance:none;
				appearance:none;
				display:block;
				float:left;
				margin-right:-2em;
				opacity:0;
				width:1em;
				z-index:-1;
			}
			input[type="checkbox"] + label,input[type="radio"] + label {
				text-decoration:none;
				cursor:pointer;
				display:inline-block;
				font-size:1em;
				font-weight:300;
				padding-left:2.4em;
				padding-right:0.75em;
				position:relative;
			}
			input[type="checkbox"] + label:before,input[type="radio"] + label:before {
				-moz-osx-font-smoothing:grayscale;
				-webkit-font-smoothing:antialiased;
				font-family:FontAwesome;
				font-style:normal;
				font-weight:normal;
				text-transform:none !important;
			}
			input[type="checkbox"] + label:before,input[type="radio"] + label:before {
				border-radius:4px;
				border:solid 1px;
				content:'';
				display:inline-block;
				height:1.65em;
				left:0;
				line-height:1.58125em;
				position:absolute;
				text-align:center;
				top:0;
				width:1.65em;
			}
			input[type="checkbox"]:checked + label:before,input[type="radio"]:checked + label:before {
				content:'\f00c';
			}
			input[type="checkbox"] + label:before {
				border-radius:4px;
			}
			input[type="radio"] + label:before {
				border-radius:100%;
			}
			::-webkit-input-placeholder {
				opacity:1.0;
			}
			:-moz-placeholder {
				opacity:1.0;
			}
			::-moz-placeholder {
				opacity:1.0;
			}
			:-ms-input-placeholder {
				opacity:1.0;
			}
			.formerize-placeholder {
				opacity:1.0;
			}
			label {
				color:#5e5e5e;
			}
			input[type="text"],input[type="password"],input[type="email"],select,textarea {
				background:rgba(144,144,144,0.075);
				border-color:#e3e3e3;
			}
			input[type="text"]:focus,input[type="password"]:focus,input[type="email"]:focus,select:focus,textarea:focus {
				border-color:#72bee1;
				box-shadow:0 0 0 1px #72bee1;
			}
			.select-wrapper:before {
				color:#e3e3e3;
			}
			input[type="checkbox"] + label,input[type="radio"] + label {
				color:#6e6e6e;
			}
			input[type="checkbox"] + label:before,input[type="radio"] + label:before {
				background:rgba(144,144,144,0.075);
				border-color:#e3e3e3;
			}
			input[type="checkbox"]:checked + label:before,input[type="radio"]:checked + label:before {
				background-color:#72bee1;
				border-color:#72bee1;
				color:#ffffff;
			}
			input[type="checkbox"]:focus + label:before,input[type="radio"]:focus + label:before {
				border-color:#72bee1;
				box-shadow:0 0 0 1px #72bee1;
			}
			::-webkit-input-placeholder {
				color:#cdcdcd !important;
			}
			:-moz-placeholder {
				color:#cdcdcd !important;
			}
			::-moz-placeholder {
				color:#cdcdcd !important;
			}
			:-ms-input-placeholder {
				color:#cdcdcd !important;
			}
			.formerize-placeholder {
				color:#cdcdcd !important;
			}
			.icon {
				text-decoration:none;
				border-bottom:none;
				position:relative;
			}
			.icon:before {
				-moz-osx-font-smoothing:grayscale;
				-webkit-font-smoothing:antialiased;
				font-family:FontAwesome;
				font-style:normal;
				font-weight:normal;
				text-transform:none !important;
			}
			.icon > .label {
				display:none;
			}
			.icon.major {
				display:block;
				margin:0 0 0.5em 0;
			}
			.icon.major:before {
				font-size:3em;
			}
			@media screen and (max-width:736px) {
				.icon.major {
					margin:0 0 1.5em 0;
				}
				.icon.major:before {
					font-size:2em;
					font-size:30px;
				}
			}.icon.major:before {
				color:#cdcdcd;
			}
			.image {
				border-radius:4px;
				border:0;
				display:inline-block;
				position:relative;
			}
			.image img {
				border-radius:4px;
				display:block;
			}
			.image.left,.image.right {
				max-width:40%;
			}
			.image.left img,.image.right img {
				width:100%;
			}
			.image.left {
				float:left;
				margin:0 1.75em 1.25em 0;
				top:0.25em;
			}
			.image.right {
				float:right;
				margin:0 0 1.25em 1.75em;
				top:0.25em;
			}
			.image.fit {
				display:block;
				margin:0 0 2em 0;
				width:100%;
			}
			.image.fit img {
				width:100%;
			}
			.image.main {
				display:block;
				margin:0 0 3em 0;
				width:100%;
			}
			.image.main img {
				width:100%;
			}
			@media screen and (max-width:736px) {
				.image.main {
					margin:0 0 2em 0;
				}
			}ol {
				list-style:decimal;
				margin:0 0 2em 0;
				padding-left:1.25em;
			}
			ol li {
				padding-left:0.25em;
			}
			ul {
				list-style:disc;
				margin:0 0 2em 0;
				padding-left:1em;
			}
			ul li {
				padding-left:0.5em;
			}
			ul.alt {
				list-style:none;
				padding-left:0;
			}
			ul.alt li {
				border-top:solid 1px;
				padding:0.5em 0;
			}
			ul.alt li:first-child {
				border-top:0;
				padding-top:0;
			}
			ul.icons {
				cursor:default;
				list-style:none;
				padding-left:0;
			}
			ul.icons li {
				display:inline-block;
				padding:0 1em 0 0;
			}
			ul.icons li:last-child {
				padding-right:0;
			}
			ul.icons li .icon:before {
				font-size:1em;
			}
			ul.actions {
				cursor:default;
				list-style:none;
				padding-left:0;
			}
			ul.actions li {
				display:inline-block;
				padding:0 1em 0 0;
				vertical-align:middle;
			}
			ul.actions li:last-child {
				padding-right:0;
			}
			ul.actions.small li {
				padding:0 0.5em 0 0;
			}
			ul.actions.vertical li {
				display:block;
				padding:1em 0 0 0;
			}
			ul.actions.vertical li:first-child {
				padding-top:0;
			}
			ul.actions.vertical li > * {
				margin-bottom:0;
			}
			ul.actions.vertical.small li {
				padding:0.5em 0 0 0;
			}
			ul.actions.vertical.small li:first-child {
				padding-top:0;
			}
			ul.actions.fit {
				display:table;
				margin-left:-1em;
				padding:0;
				table-layout:fixed;
				width:calc(100% + 1em);
			}
			ul.actions.fit li {
				display:table-cell;
				padding:0 0 0 1em;
			}
			ul.actions.fit li > * {
				margin-bottom:0;
			}
			ul.actions.fit.small {
				margin-left:-0.5em;
				width:calc(100% + 0.5em);
			}
			ul.actions.fit.small li {
				padding:0 0 0 0.5em;
			}
			@media screen and (max-width:480px) {
				ul.actions {
					margin:0 0 2em 0;
				}
				ul.actions li {
					padding:1em 0 0 0;
					display:block;
					text-align:center;
					width:100%;
				}
				ul.actions li:first-child {
					padding-top:0;
				}
				ul.actions li > * {
					width:100%;
					margin:0 !important;
				}
				ul.actions li > *.icon:before {
					margin-left:-2em;
				}
				ul.actions.small li {
					padding:0.5em 0 0 0;
				}
				ul.actions.small li:first-child {
					padding-top:0;
				}
			}ul.grid-icons {
				display:-moz-flex;
				display:-webkit-flex;
				display:-ms-flex;
				display:flex;
				-moz-flex-wrap:wrap;
				-webkit-flex-wrap:wrap;
				-ms-flex-wrap:wrap;
				flex-wrap:wrap;
				border-style:solid;
				border-width:1px;
				border-left-width:0;
				list-style:none;
				padding:0;
			}
			ul.grid-icons li {
				-moz-align-items:center;
				-webkit-align-items:center;
				-ms-align-items:center;
				align-items:center;
				display:-moz-flex;
				display:-webkit-flex;
				display:-ms-flex;
				display:flex;
				border-style:solid;
				border-width:0;
				border-left-width:1px;
				border-top-width:1px;
				min-height:13em;
				padding:0;
				text-align:center;
				width:50%;
			}
			ul.grid-icons li:nth-child(1),ul.grid-icons li:nth-child(2) {
				border-top-width:0;
			}
			ul.grid-icons li:nth-child(2n - 1) {
				border-left-width:0;
			}
			ul.grid-icons li > .inner {
				width:100%;
			}
			ul.grid-icons li > .inner .icon {
				margin:0;
			}
			ul.grid-icons li > .inner h3 {
				font-size:1em;
				font-weight:600;
				margin:0;
			}
			@media screen and (max-width:736px) {
				ul.grid-icons li {
					min-height:11em;
				}
			}ul.faces {
				display:-moz-flex;
				display:-webkit-flex;
				display:-ms-flex;
				display:flex;
				list-style:none;
				margin:3.5em 0;
				padding:0;
			}
			ul.faces:last-child {
				margin-bottom:2em;
			}
			ul.faces li {
				-moz-flex:1;
				-webkit-flex:1;
				-ms-flex:1;
				flex:1;
				border-style:solid;
				border-left-width:1px;
				margin-left:3em;
				padding:0;
				padding-left:3em;
			}
			ul.faces li:first-child {
				border-left:0;
				margin-left:0;
				padding-left:0;
			}
			ul.faces li h3 {
				font-size:1em;
				font-weight:600;
			}
			ul.faces li .image {
				margin:0 0 2em 0;
			}
			ul.faces li .image img {
				border-radius:100%;
			}
			ul.faces li p {
				font-style:italic;
			}
			ul.faces li >:last-child {
				margin-bottom:0;
			}
			@media screen and (max-width:980px) {
				ul.faces {
					-moz-flex-direction:column;
					-webkit-flex-direction:column;
					-ms-flex-direction:column;
					flex-direction:column;
				}
				ul.faces li {
					border-left-width:0;
					border-top-width:1px;
					margin-left:0;
					padding-left:0;
					margin-top:3em;
					padding-top:3em;
					-ms-flex:0 1 auto;
				}
				ul.faces li:first-child {
					border-top-width:0;
					margin-top:0;
					padding-top:0;
				}
			}ul.major-icons {
				display:-moz-flex;
				display:-webkit-flex;
				display:-ms-flex;
				display:flex;
				-moz-justify-content:center;
				-webkit-justify-content:center;
				-ms-justify-content:center;
				justify-content:center;
				list-style:none;
				margin:3em 0;
				padding:0;
			}
			ul.major-icons:last-child {
				margin-bottom:2em;
			}
			ul.major-icons li {
				display:inline-block;
				border-style:solid;
				border-left-width:1px;
				min-width:15em;
				padding:0;
			}
			ul.major-icons li:first-child {
				border-left:0;
				margin-left:0;
				padding-left:0;
			}
			ul.major-icons li h3 {
				font-size:0.8em;
			}
			ul.major-icons li >:last-child {
				margin-bottom:0;
			}
			@media screen and (max-width:736px) {
				ul.major-icons {
					-moz-flex-direction:column;
					-webkit-flex-direction:column;
					-ms-flex-direction:column;
					flex-direction:column;
				}
				ul.major-icons li {
					min-width:0;
					width:100%;
					border-left-width:0;
					border-top-width:1px;
					margin:2em 0 0 0;
					padding:2em 0 0 0;
				}
				ul.major-icons li:first-child {
					border-top:0;
					margin-top:0;
					padding-top:0;
				}
				ul.major-icons li h3 {
					font-size:1em;
				}
			}ul.joined-icons {
				display:-moz-flex;
				display:-webkit-flex;
				display:-ms-flex;
				display:flex;
				-moz-justify-content:center;
				-webkit-justify-content:center;
				-ms-justify-content:center;
				justify-content:center;
				list-style:none;
				padding:0;
			}
			ul.joined-icons li {
				border-style:solid;
				border-width:1px;
				border-left-width:0;
				padding:0;
			}
			ul.joined-icons li a {
				display:block;
				width:2.5em;
				height:2.5em;
				line-height:2.5em;
			}
			ul.joined-icons li:first-child {
				border-left-width:1px;
				border-top-left-radius:4px;
				border-bottom-left-radius:4px;
			}
			ul.joined-icons li:last-child {
				border-top-right-radius:4px;
				border-bottom-right-radius:4px;
			}
			dl {
				margin:0 0 2em 0;
			}
			dl dt {
				display:block;
				font-weight:300;
				margin:0 0 1em 0;
			}
			dl dd {
				margin-left:2em;
			}
			ul.alt li {
				border-top-color:#e3e3e3;
			}
			ul.grid-icons {
				border-color:#e3e3e3;
			}
			ul.grid-icons li {
				border-color:#e3e3e3;
			}
			ul.faces li {
				border-color:#e3e3e3;
			}
			ul.major-icons li {
				border-color:#e3e3e3;
			}
			ul.joined-icons li {
				border-color:#e3e3e3;
			}
			ul.joined-icons li a {
				color:#cdcdcd;
			}
			.logo {
				text-decoration:none;
				border:0;
				color:inherit;
				display:block;
				margin:0 0 1.5em 0;
			}
			.logo:before {
				-moz-osx-font-smoothing:grayscale;
				-webkit-font-smoothing:antialiased;
				font-family:FontAwesome;
				font-style:normal;
				font-weight:normal;
				text-transform:none !important;
			}
			.logo:before {
				background:#72bee1;
				border-radius:4px;
				color:#ffffff;
				content:'\f0e7';
				display:inline-block;
				font-size:3em;
				height:1.65em;
				line-height:1.65em;
				text-align:center;
				width:1.65em;
			}
			@media screen and (max-width:1280px) {
				.logo:before {
					font-size:2.5em;
					font-size:40px;
				}
			}@media screen and (max-width:736px) {
				.logo {
					margin:0 0 1em 0;
				}
				.logo:before {
					font-size:2em;
					font-size:30px;
				}
			}section.special,article.special {
				text-align:center;
			}
			header p {
				position:relative;
				margin:0 0 1.5em 0;
			}
			header h2 + p {
				font-size:1.25em;
				margin-top:-1em;
			}
			header h3 + p {
				font-size:1.1em;
				margin-top:-0.8em;
			}
			header h4 + p,header h5 + p,header h6 + p {
				font-size:0.9em;
				margin-top:-0.6em;
			}
			header p {
				color:#cdcdcd;
			}
			.table-wrapper {
				-webkit-overflow-scrolling:touch;
				overflow-x:auto;
			}
			table {
				margin:0 0 2em 0;
				width:100%;
			}
			table tbody tr {
				border:solid 1px;
				border-left:0;
				border-right:0;
			}
			table td {
				padding:0.75em 0.75em;
			}
			table th {
				font-size:0.9em;
				font-weight:300;
				padding:0 0.75em 0.75em 0.75em;
				text-align:left;
			}
			table thead {
				border-bottom:solid 2px;
			}
			table tfoot {
				border-top:solid 2px;
			}
			table.alt {
				border-collapse:separate;
			}
			table.alt tbody tr td {
				border:solid 1px;
				border-left-width:0;
				border-top-width:0;
			}
			table.alt tbody tr td:first-child {
				border-left-width:1px;
			}
			table.alt tbody tr:first-child td {
				border-top-width:1px;
			}
			table.alt thead {
				border-bottom:0;
			}
			table.alt tfoot {
				border-top:0;
			}
			table tbody tr {
				border-color:#e3e3e3;
			}
			table tbody tr:nth-child(2n + 1) {
				background-color:rgba(144,144,144,0.075);
			}
			table th {
				color:#5e5e5e;
			}
			table thead {
				border-bottom-color:#e3e3e3;
			}
			table tfoot {
				border-top-color:#e3e3e3;
			}
			table.alt tbody tr td {
				border-color:#e3e3e3;
			}
			.banner {
				background-color:#4a4a4a;
				color:#ffffff;
				padding:7em 0 5em 0;
				background-image:-moz-linear-gradient(to top,rgba(74,74,74,0.50),rgba(74,74,74,0.50)),url("images/pic01.jpg");
				background-image:-webkit-linear-gradient(to top,rgba(74,74,74,0.50),rgba(74,74,74,0.50)),url("images/pic01.jpg");
				background-image:-ms-linear-gradient(to top,rgba(74,74,74,0.50),rgba(74,74,74,0.50)),url("images/pic01.jpg");
				background-image:linear-gradient(to top,rgba(74,74,74,0.50),rgba(74,74,74,0.50)),url("images/pic01.jpg");
				background-position:center;
				background-repeat:no-repeat;
				background-size:cover;
				position:relative;
				text-align:center;
			}
			.banner input[type="submit"],.banner input[type="reset"],.banner input[type="button"],.banner button,.banner .button {
				background-color:transparent;
				box-shadow:inset 0 0 0 1px #ffffff;
				color:#ffffff !important;
			}
			.banner input[type="submit"]:hover,.banner input[type="reset"]:hover,.banner input[type="button"]:hover,.banner button:hover,.banner .button:hover {
				background-color:rgba(255,255,255,0.075);
				color:#ffffff !important;
			}
			.banner input[type="submit"]:active,.banner input[type="reset"]:active,.banner input[type="button"]:active,.banner button:active,.banner .button:active {
				background-color:rgba(255,255,255,0.2);
			}
			.banner input[type="submit"].special,.banner input[type="reset"].special,.banner input[type="button"].special,.banner button.special,.banner .button.special {
				box-shadow:none;
				background-color:#ffffff;
				color:#4a4a4a !important;
			}
			.banner input,.banner select,.banner textarea {
				color:#ffffff;
			}
			.banner a:hover {
				color:#ffffff !important;
			}
			.banner strong,.banner b {
				color:#ffffff;
			}
			.banner h1,.banner h2,.banner h3,.banner h4,.banner h5,.banner h6 {
				color:#ffffff;
			}
			.banner blockquote {
				border-left-color:#ffffff;
			}
			.banner code {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.banner hr {
				border-bottom-color:#ffffff;
			}
			.banner h2 {
				font-size:2.5em;
				margin:0 0 1em 0;
			}
			.banner > * {
				-moz-transition:opacity 2s ease-in-out;
				-webkit-transition:opacity 2s ease-in-out;
				-ms-transition:opacity 2s ease-in-out;
				transition:opacity 2s ease-in-out;
				-moz-transition-delay:1.25s;
				-webkit-transition-delay:1.25s;
				-ms-transition-delay:1.25s;
				transition-delay:1.25s;
				opacity:1;
			}
			@media screen and (max-width:1280px) {
				.banner {
					padding:5em 0 3em 0;
				}
				.banner h2 {
					font-size:2em;
				}
			}@media screen and (max-width:980px) {
				.banner {
					padding:8em 3em 6em 3em;
				}
			}@media screen and (max-width:736px) {
				.banner {
					padding:4em 3em 2em 3em;
				}
				.banner h2 {
					font-size:1.75em;
				}
			}@media screen and (max-width:360px) {
				.banner {
					padding:4em 2em 2em 2em;
				}
				.banner h2 {
					font-size:1.5em;
				}
			}body.is-loading .banner > * {
				opacity:0;
			}
			.wrapper {
				padding:5em 5em 3em 5em;
				position:relative;
			}
			.wrapper.style1 {
				background-color:#72bee1;
				color:#ffffff;
			}
			.wrapper.style1 input,.wrapper.style1 select,.wrapper.style1 textarea {
				color:#ffffff;
			}
			.wrapper.style1 a:hover {
				color:#ffffff !important;
			}
			.wrapper.style1 strong,.wrapper.style1 b {
				color:#ffffff;
			}
			.wrapper.style1 h1,.wrapper.style1 h2,.wrapper.style1 h3,.wrapper.style1 h4,.wrapper.style1 h5,.wrapper.style1 h6 {
				color:#ffffff;
			}
			.wrapper.style1 blockquote {
				border-left-color:#ffffff;
			}
			.wrapper.style1 code {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style1 hr {
				border-bottom-color:#ffffff;
			}
			.wrapper.style1 .box {
				border-color:#ffffff;
			}
			.wrapper.style1 input[type="submit"],.wrapper.style1 input[type="reset"],.wrapper.style1 input[type="button"],.wrapper.style1 button,.wrapper.style1 .button {
				background-color:transparent;
				box-shadow:inset 0 0 0 1px #ffffff;
				color:#ffffff !important;
			}
			.wrapper.style1 input[type="submit"]:hover,.wrapper.style1 input[type="reset"]:hover,.wrapper.style1 input[type="button"]:hover,.wrapper.style1 button:hover,.wrapper.style1 .button:hover {
				background-color:rgba(255,255,255,0.075);
				color:#ffffff !important;
			}
			.wrapper.style1 input[type="submit"]:active,.wrapper.style1 input[type="reset"]:active,.wrapper.style1 input[type="button"]:active,.wrapper.style1 button:active,.wrapper.style1 .button:active {
				background-color:rgba(255,255,255,0.2);
			}
			.wrapper.style1 input[type="submit"].special,.wrapper.style1 input[type="reset"].special,.wrapper.style1 input[type="button"].special,.wrapper.style1 button.special,.wrapper.style1 .button.special {
				box-shadow:none;
				background-color:#ffffff;
				color:#72bee1 !important;
			}
			.wrapper.style1 label {
				color:#ffffff;
			}
			.wrapper.style1 input[type="text"],.wrapper.style1 input[type="password"],.wrapper.style1 input[type="email"],.wrapper.style1 select,.wrapper.style1 textarea {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style1 input[type="text"]:focus,.wrapper.style1 input[type="password"]:focus,.wrapper.style1 input[type="email"]:focus,.wrapper.style1 select:focus,.wrapper.style1 textarea:focus {
				border-color:#ffffff;
				box-shadow:0 0 0 1px #ffffff;
			}
			.wrapper.style1 .select-wrapper:before {
				color:#ffffff;
			}
			.wrapper.style1 input[type="checkbox"] + label,.wrapper.style1 input[type="radio"] + label {
				color:#ffffff;
			}
			.wrapper.style1 input[type="checkbox"] + label:before,.wrapper.style1 input[type="radio"] + label:before {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style1 input[type="checkbox"]:checked + label:before,.wrapper.style1 input[type="radio"]:checked + label:before {
				background-color:#ffffff;
				border-color:#ffffff;
				color:#72bee1;
			}
			.wrapper.style1 input[type="checkbox"]:focus + label:before,.wrapper.style1 input[type="radio"]:focus + label:before {
				border-color:#ffffff;
				box-shadow:0 0 0 1px #ffffff;
			}
			.wrapper.style1::-webkit-input-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style1:-moz-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style1::-moz-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style1:-ms-input-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style1 .formerize-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style1 .icon.major:before {
				color:#ffffff;
			}
			.wrapper.style1 ul.alt li {
				border-top-color:#ffffff;
			}
			.wrapper.style1 ul.grid-icons {
				border-color:#ffffff;
			}
			.wrapper.style1 ul.grid-icons li {
				border-color:#ffffff;
			}
			.wrapper.style1 ul.faces li {
				border-color:#ffffff;
			}
			.wrapper.style1 ul.major-icons li {
				border-color:#ffffff;
			}
			.wrapper.style1 ul.joined-icons li {
				border-color:#ffffff;
			}
			.wrapper.style1 ul.joined-icons li a {
				color:#ffffff;
			}
			.wrapper.style1 header p {
				color:#ffffff;
			}
			.wrapper.style1 table tbody tr {
				border-color:#ffffff;
			}
			.wrapper.style1 table tbody tr:nth-child(2n + 1) {
				background-color:rgba(255,255,255,0.075);
			}
			.wrapper.style1 table th {
				color:#ffffff;
			}
			.wrapper.style1 table thead {
				border-bottom-color:#ffffff;
			}
			.wrapper.style1 table tfoot {
				border-top-color:#ffffff;
			}
			.wrapper.style1 table.alt tbody tr td {
				border-color:#ffffff;
			}
			.wrapper.style2 {
				background-color:#63b6b5;
				color:#ffffff;
			}
			.wrapper.style2 input,.wrapper.style2 select,.wrapper.style2 textarea {
				color:#ffffff;
			}
			.wrapper.style2 a:hover {
				color:#ffffff !important;
			}
			.wrapper.style2 strong,.wrapper.style2 b {
				color:#ffffff;
			}
			.wrapper.style2 h1,.wrapper.style2 h2,.wrapper.style2 h3,.wrapper.style2 h4,.wrapper.style2 h5,.wrapper.style2 h6 {
				color:#ffffff;
			}
			.wrapper.style2 blockquote {
				border-left-color:#ffffff;
			}
			.wrapper.style2 code {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style2 hr {
				border-bottom-color:#ffffff;
			}
			.wrapper.style2 .box {
				border-color:#ffffff;
			}
			.wrapper.style2 input[type="submit"],.wrapper.style2 input[type="reset"],.wrapper.style2 input[type="button"],.wrapper.style2 button,.wrapper.style2 .button {
				background-color:transparent;
				box-shadow:inset 0 0 0 1px #ffffff;
				color:#ffffff !important;
			}
			.wrapper.style2 input[type="submit"]:hover,.wrapper.style2 input[type="reset"]:hover,.wrapper.style2 input[type="button"]:hover,.wrapper.style2 button:hover,.wrapper.style2 .button:hover {
				background-color:rgba(255,255,255,0.075);
				color:#ffffff !important;
			}
			.wrapper.style2 input[type="submit"]:active,.wrapper.style2 input[type="reset"]:active,.wrapper.style2 input[type="button"]:active,.wrapper.style2 button:active,.wrapper.style2 .button:active {
				background-color:rgba(255,255,255,0.2);
			}
			.wrapper.style2 input[type="submit"].special,.wrapper.style2 input[type="reset"].special,.wrapper.style2 input[type="button"].special,.wrapper.style2 button.special,.wrapper.style2 .button.special {
				box-shadow:none;
				background-color:#ffffff;
				color:#63b6b5 !important;
			}
			.wrapper.style2 label {
				color:#ffffff;
			}
			.wrapper.style2 input[type="text"],.wrapper.style2 input[type="password"],.wrapper.style2 input[type="email"],.wrapper.style2 select,.wrapper.style2 textarea {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style2 input[type="text"]:focus,.wrapper.style2 input[type="password"]:focus,.wrapper.style2 input[type="email"]:focus,.wrapper.style2 select:focus,.wrapper.style2 textarea:focus {
				border-color:#ffffff;
				box-shadow:0 0 0 1px #ffffff;
			}
			.wrapper.style2 .select-wrapper:before {
				color:#ffffff;
			}
			.wrapper.style2 input[type="checkbox"] + label,.wrapper.style2 input[type="radio"] + label {
				color:#ffffff;
			}
			.wrapper.style2 input[type="checkbox"] + label:before,.wrapper.style2 input[type="radio"] + label:before {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style2 input[type="checkbox"]:checked + label:before,.wrapper.style2 input[type="radio"]:checked + label:before {
				background-color:#ffffff;
				border-color:#ffffff;
				color:#63b6b5;
			}
			.wrapper.style2 input[type="checkbox"]:focus + label:before,.wrapper.style2 input[type="radio"]:focus + label:before {
				border-color:#ffffff;
				box-shadow:0 0 0 1px #ffffff;
			}
			.wrapper.style2::-webkit-input-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style2:-moz-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style2::-moz-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style2:-ms-input-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style2 .formerize-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style2 .icon.major:before {
				color:#ffffff;
			}
			.wrapper.style2 ul.alt li {
				border-top-color:#ffffff;
			}
			.wrapper.style2 ul.grid-icons {
				border-color:#ffffff;
			}
			.wrapper.style2 ul.grid-icons li {
				border-color:#ffffff;
			}
			.wrapper.style2 ul.faces li {
				border-color:#ffffff;
			}
			.wrapper.style2 ul.major-icons li {
				border-color:#ffffff;
			}
			.wrapper.style2 ul.joined-icons li {
				border-color:#ffffff;
			}
			.wrapper.style2 ul.joined-icons li a {
				color:#ffffff;
			}
			.wrapper.style2 header p {
				color:#ffffff;
			}
			.wrapper.style2 table tbody tr {
				border-color:#ffffff;
			}
			.wrapper.style2 table tbody tr:nth-child(2n + 1) {
				background-color:rgba(255,255,255,0.075);
			}
			.wrapper.style2 table th {
				color:#ffffff;
			}
			.wrapper.style2 table thead {
				border-bottom-color:#ffffff;
			}
			.wrapper.style2 table tfoot {
				border-top-color:#ffffff;
			}
			.wrapper.style2 table.alt tbody tr td {
				border-color:#ffffff;
			}
			.wrapper.style3 {
				background-color:#e78e84;
				color:#ffffff;
			}
			.wrapper.style3 input,.wrapper.style3 select,.wrapper.style3 textarea {
				color:#ffffff;
			}
			.wrapper.style3 a:hover {
				color:#ffffff !important;
			}
			.wrapper.style3 strong,.wrapper.style3 b {
				color:#ffffff;
			}
			.wrapper.style3 h1,.wrapper.style3 h2,.wrapper.style3 h3,.wrapper.style3 h4,.wrapper.style3 h5,.wrapper.style3 h6 {
				color:#ffffff;
			}
			.wrapper.style3 blockquote {
				border-left-color:#ffffff;
			}
			.wrapper.style3 code {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style3 hr {
				border-bottom-color:#ffffff;
			}
			.wrapper.style3 .box {
				border-color:#ffffff;
			}
			.wrapper.style3 input[type="submit"],.wrapper.style3 input[type="reset"],.wrapper.style3 input[type="button"],.wrapper.style3 button,.wrapper.style3 .button {
				background-color:transparent;
				box-shadow:inset 0 0 0 1px #ffffff;
				color:#ffffff !important;
			}
			.wrapper.style3 input[type="submit"]:hover,.wrapper.style3 input[type="reset"]:hover,.wrapper.style3 input[type="button"]:hover,.wrapper.style3 button:hover,.wrapper.style3 .button:hover {
				background-color:rgba(255,255,255,0.075);
				color:#ffffff !important;
			}
			.wrapper.style3 input[type="submit"]:active,.wrapper.style3 input[type="reset"]:active,.wrapper.style3 input[type="button"]:active,.wrapper.style3 button:active,.wrapper.style3 .button:active {
				background-color:rgba(255,255,255,0.2);
			}
			.wrapper.style3 input[type="submit"].special,.wrapper.style3 input[type="reset"].special,.wrapper.style3 input[type="button"].special,.wrapper.style3 button.special,.wrapper.style3 .button.special {
				box-shadow:none;
				background-color:#ffffff;
				color:#e78e84 !important;
			}
			.wrapper.style3 label {
				color:#ffffff;
			}
			.wrapper.style3 input[type="text"],.wrapper.style3 input[type="password"],.wrapper.style3 input[type="email"],.wrapper.style3 select,.wrapper.style3 textarea {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style3 input[type="text"]:focus,.wrapper.style3 input[type="password"]:focus,.wrapper.style3 input[type="email"]:focus,.wrapper.style3 select:focus,.wrapper.style3 textarea:focus {
				border-color:#ffffff;
				box-shadow:0 0 0 1px #ffffff;
			}
			.wrapper.style3 .select-wrapper:before {
				color:#ffffff;
			}
			.wrapper.style3 input[type="checkbox"] + label,.wrapper.style3 input[type="radio"] + label {
				color:#ffffff;
			}
			.wrapper.style3 input[type="checkbox"] + label:before,.wrapper.style3 input[type="radio"] + label:before {
				background:rgba(255,255,255,0.075);
				border-color:#ffffff;
			}
			.wrapper.style3 input[type="checkbox"]:checked + label:before,.wrapper.style3 input[type="radio"]:checked + label:before {
				background-color:#ffffff;
				border-color:#ffffff;
				color:#e78e84;
			}
			.wrapper.style3 input[type="checkbox"]:focus + label:before,.wrapper.style3 input[type="radio"]:focus + label:before {
				border-color:#ffffff;
				box-shadow:0 0 0 1px #ffffff;
			}
			.wrapper.style3::-webkit-input-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style3:-moz-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style3::-moz-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style3:-ms-input-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style3 .formerize-placeholder {
				color:#ffffff !important;
			}
			.wrapper.style3 .icon.major:before {
				color:#ffffff;
			}
			.wrapper.style3 ul.alt li {
				border-top-color:#ffffff;
			}
			.wrapper.style3 ul.grid-icons {
				border-color:#ffffff;
			}
			.wrapper.style3 ul.grid-icons li {
				border-color:#ffffff;
			}
			.wrapper.style3 ul.faces li {
				border-color:#ffffff;
			}
			.wrapper.style3 ul.major-icons li {
				border-color:#ffffff;
			}
			.wrapper.style3 ul.joined-icons li {
				border-color:#ffffff;
			}
			.wrapper.style3 ul.joined-icons li a {
				color:#ffffff;
			}
			.wrapper.style3 header p {
				color:#ffffff;
			}
			.wrapper.style3 table tbody tr {
				border-color:#ffffff;
			}
			.wrapper.style3 table tbody tr:nth-child(2n + 1) {
				background-color:rgba(255,255,255,0.075);
			}
			.wrapper.style3 table th {
				color:#ffffff;
			}
			.wrapper.style3 table thead {
				border-bottom-color:#ffffff;
			}
			.wrapper.style3 table tfoot {
				border-top-color:#ffffff;
			}
			.wrapper.style3 table.alt tbody tr td {
				border-color:#ffffff;
			}
			.wrapper.spotlight {
				display:-moz-flex;
				display:-webkit-flex;
				display:-ms-flex;
				display:flex;
				-moz-align-items:stretch;
				-webkit-align-items:stretch;
				-ms-align-items:stretch;
				align-items:stretch;
				padding:0;
			}
			.wrapper.spotlight > * {
				margin:0;
			}
			.wrapper.spotlight > * > * {
				-ms-flex:0 1 auto;
			}
			.wrapper.spotlight >:first-child {
				width:60%;
			}
			.wrapper.spotlight >:last-child {
				width:40%;
			}
			.wrapper.spotlight > .content {
				display:-moz-flex;
				display:-webkit-flex;
				display:-ms-flex;
				display:flex;
				-moz-align-items:center;
				-webkit-align-items:center;
				-ms-align-items:center;
				align-items:center;
				padding:5em 5em 3em 5em;
			}
			.wrapper.spotlight > .grid-icons {
				border-width:0px;
				border-left-width:1px;
			}
			.wrapper.spotlight > .image {
				border-radius:0;
				background-repeat:no-repeat;
				background-position:center;
				background-size:cover;
				display:block;
			}
			.wrapper.spotlight.alt {
				-moz-flex-direction:row-reverse;
				-webkit-flex-direction:row-reverse;
				-ms-flex-direction:row-reverse;
				flex-direction:row-reverse;
			}
			@media screen and (max-width:1280px) {
				.wrapper {
					padding:4em 4em 2em 4em;
				}
				.wrapper.spotlight > .content {
					padding:4em 4em 2em 4em;
				}
			}@media screen and (max-width:980px) {
				.wrapper.spotlight {
					-moz-flex-direction:column;
					-webkit-flex-direction:column;
					-ms-flex-direction:column;
					flex-direction:column;
					text-align:center;
				}
				.wrapper.spotlight >:first-child {
					width:100%;
				}
				.wrapper.spotlight >:last-child {
					width:100%;
				}
				.wrapper.spotlight > .grid-icons {
					border-width:0px;
					border-top-width:1px;
				}
				.wrapper.spotlight > .content + .image {
					-moz-order:-1;
					-webkit-order:-1;
					-ms-order:-1;
					order:-1;
				}
				.wrapper.spotlight > .image {
					height:40em;
					max-height:50vh;
				}
				.wrapper.spotlight.alt {
					-moz-flex-direction:column;
					-webkit-flex-direction:column;
					-ms-flex-direction:column;
					flex-direction:column;
				}
			}@media screen and (max-width:736px) {
				.wrapper {
					padding:3em 3em 1em 3em;
				}
				.wrapper.spotlight > .content {
					padding:3em 3em 1em 3em;
				}
			}@media screen and (max-width:480px) {
				.wrapper {
					padding:3em 2em 1em 2em;
				}
				.wrapper.spotlight > .content {
					padding:3em 2em 1em 2em;
				}
			}@-moz-keyframes reveal-header {
				0% {
					top:-4em;
					opacity:0;
				}
				100% {
					top:0;
					opacity:1;
				}
			}@-webkit-keyframes reveal-header {
				0% {
					top:-4em;
					opacity:0;
				}
				100% {
					top:0;
					opacity:1;
				}
			}@-ms-keyframes reveal-header {
				0% {
					top:-4em;
					opacity:0;
				}
				100% {
					top:0;
					opacity:1;
				}
			}@keyframes reveal-header {
				0% {
					top:-4em;
					opacity:0;
				}
				100% {
					top:0;
					opacity:1;
				}
			}body {
				padding-top:8em;
			}
			#header {
				display:-moz-flex;
				display:-webkit-flex;
				display:-ms-flex;
				display:flex;
				background:#ffffff;
				box-shadow:0 0 0.15em 0 rgba(0,0,0,0.075);
				height:3.5em;
				left:0;
				line-height:3.5em;
				padding:0 2em;
				position:fixed;
				top:0;
				width:100%;
				z-index:10001;
			}
			#header > .logo {
				display:inline-block;
				margin:0;
				white-space:nowrap;
			}
			#header > .logo:before {
				font-size:1em;
				font-size:16px;
				margin-left:-1em;
				margin-right:0.75em;
			}
			#header > nav {
				width:100%;
				text-align:right;
			}
			#header > nav > ul {
				list-style:none;
				padding:0;
				margin:0;
			}
			#header > nav > ul > li {
				display:inline-block;
				margin-left:1em;
				padding:0;
			}
			#header > nav > ul > li > ul {
				display:none;
			}
			#header > nav > ul > li a {
				color:inherit;
				border:0;
				padding:0.35em 0.75em;
				border-radius:4px;
			}
			#header > nav > ul > li:first-child {
				margin-left:0 !important;
			}
			#header > nav > ul > li.dropotron-active a {
				color:inherit !important;
				background-color:rgba(144,144,144,0.075);
			}
			#header > nav > ul > li.active a {
				color:#5e5e5e;
				font-weight:600;
				position:relative;
			}
			#header > nav > ul > li.active a:after {
				-moz-transition:background-color 0.2s ease-in-out;
				-webkit-transition:background-color 0.2s ease-in-out;
				-ms-transition:background-color 0.2s ease-in-out;
				transition:background-color 0.2s ease-in-out;
				background-color:#e3e3e3;
				border-radius:0.175em;
				bottom:-0.05em;
				content:'';
				height:0.175em;
				left:0.5em;
				position:absolute;
				width:calc(100% - 1em);
			}
			#header > nav > ul > li.active a:hover:after {
				background-color:#72bee1;
			}
			#header.reveal {
				-moz-animation:reveal-header 0.5s ease;
				-webkit-animation:reveal-header 0.5s ease;
				-ms-animation:reveal-header 0.5s ease;
				animation:reveal-header 0.5s ease;
			}
			#header.alt {
				-moz-align-items:center;
				-webkit-align-items:center;
				-ms-align-items:center;
				align-items:center;
				-moz-animation:none;
				-webkit-animation:none;
				-ms-animation:none;
				animation:none;
				background-color:transparent;
				box-shadow:none;
				height:8em;
				position:absolute;
			}
			#header.alt > .logo {
				display:none;
			}
			#header.alt > nav {
				text-align:center;
			}
			#header.alt > nav > ul > li {
				margin-left:1.5em;
			}
			@media screen and (max-width:1280px) {
				#header > nav > ul > li {
					margin-left:0.5em;
				}
				#header.alt > nav > ul > li {
					margin-left:1em;
				}
			}.dropotron {
				background:#fff;
				border-radius:4px;
				box-shadow:0 0.05em 0.175em 0 rgba(0,0,0,0.1);
				list-style:none;
				margin-top:-0.75em;
				min-width:10em;
				padding:0.75em 0;
			}
			.dropotron li {
				padding:0;
			}
			.dropotron li a {
				-moz-transition:none !important;
				-webkit-transition:none !important;
				-ms-transition:none !important;
				transition:none !important;
				border:0;
				box-shadow:inset 0 1px 0 0 rgba(144,144,144,0.075);
				color:inherit;
				display:block;
				line-height:2.25em;
				padding:0 1em;
			}
			.dropotron li:first-child > a {
				box-shadow:none;
			}
			.dropotron li.active > a,.dropotron li:hover > a {
				background:#72bee1;
				color:#ffffff !important;
			}
			.dropotron.level-0 {
				font-size:0.9em;
				margin-top:0.75em;
			}
			.dropotron.level-0:before {
				-moz-transform:rotate(45deg);
				-webkit-transform:rotate(45deg);
				-o-transform:rotate(45deg);
				-ms-transform:rotate(45deg);
				transform:rotate(45deg);
				background:#fff;
				content:'';
				display:block;
				height:0.75em;
				position:absolute;
				left:50%;
				top:-0.375em;
				width:0.75em;
				margin-left:-0.375em;
				box-shadow:inset 1px 1px 0 0 rgba(0,0,0,0.075);
				box-shadow:-1px -1px 2px 0 rgba(0,0,0,0.05);
			}
			@media screen and (max-width:1280px) {
				body {
					padding-top:6em;
				}
				#header.alt {
					height:6em;
				}
			}@media screen and (max-width:980px) {
				body {
					padding-top:0;
				}
				#header {
					display:none;
				}
			}#main {
				background-color:#ffffff;
				border-radius:1.5em;
				margin:0 auto;
				width:70em;
				max-width:calc(100% - 4em);
			}
			#main > header {
				padding:4em 0 2em 0;
				text-align:center;
			}
			#main > header .logo {
				-moz-transition:-moz-transform 1s ease,opacity 1s ease-in-out;
				-webkit-transition:-webkit-transform 1s ease,opacity 1s ease-in-out;
				-ms-transition:-ms-transform 1s ease,opacity 1s ease-in-out;
				transition:transform 1s ease,opacity 1s ease-in-out;
				-moz-transform:translateY(0);
				-webkit-transform:translateY(0);
				-ms-transform:translateY(0);
				transform:translateY(0);
				opacity:1;
			}
			#main > header .logo + h1 {
				-moz-transition:opacity 1s ease-in-out;
				-webkit-transition:opacity 1s ease-in-out;
				-ms-transition:opacity 1s ease-in-out;
				transition:opacity 1s ease-in-out;
				-moz-transition-delay:0.35s;
				-webkit-transition-delay:0.35s;
				-ms-transition-delay:0.35s;
				transition-delay:0.35s;
				font-size:1.35em;
				opacity:1;
			}
			#main > #content {
				padding-top:0;
			}
			@media screen and (max-width:980px) {
				#main {
					border-radius:0;
					max-width:100%;
				}
				#main > header {
					padding:5em 0 3em 0;
				}
			}@media screen and (max-width:736px) {
				#main > header {
					padding:4em 0 2em 0;
				}
			}body.is-loading #main > header .logo {
				-moz-transform:translateY(1em);
				-webkit-transform:translateY(1em);
				-ms-transform:translateY(1em);
				transform:translateY(1em);
				opacity:0;
			}
			body.is-loading #main > header .logo + h1 {
				opacity:0;
			}
			#footer {
				padding:5em 5em 3em 5em;
				margin:0 auto;
				text-align:center;
				width:70em;
				max-width:calc(100% - 4em);
			}
			#footer .copyright {
				color:#cdcdcd;
				margin:4em 0 2em 0;
			}
			@media screen and (max-width:1280px) {
				#footer {
					padding:4em 4em 2em 4em;
				}
			}@media screen and (max-width:980px) {
				#footer {
					max-width:100%;
				}
			}@media screen and (max-width:736px) {
				#footer {
					padding:3em 3em 1em 3em;
				}
			}@media screen and (max-width:480px) {
				#footer {
					padding:2em 2em 0.1em 2em;
				}
			}#navPanel {
				-moz-transform:translateX(-20em);
				-webkit-transform:translateX(-20em);
				-ms-transform:translateX(-20em);
				transform:translateX(-20em);
				-moz-transition:-moz-transform 0.5s ease,box-shadow 0.5s ease,visibility 0.5s;
				-webkit-transition:-webkit-transform 0.5s ease,box-shadow 0.5s ease,visibility 0.5s;
				-ms-transition:-ms-transform 0.5s ease,box-shadow 0.5s ease,visibility 0.5s;
				transition:transform 0.5s ease,box-shadow 0.5s ease,visibility 0.5s;
				-webkit-overflow-scrolling:touch;
				background-color:#ffffff;
				box-shadow:none;
				display:none;
				height:100%;
				max-width:80%;
				overflow-y:auto;
				position:fixed;
				left:0;
				top:0;
				visibility:hidden;
				width:20em;
				z-index:10002;
			}
			#navPanel nav {
				padding:3em 2em;
			}
			#navPanel .link {
				border:0;
				border-top:solid 1px #e3e3e3;
				color:inherit !important;
				display:block;
				font-size:0.9em;
				padding:1em 0;
			}
			#navPanel .link:first-child {
				border-top:0;
			}
			#navPanel .link.depth-0 {
				font-weight:600;
				color:#5e5e5e !important;
			}
			#navPanel .link .indent-1 {
				display:inline-block;
				width:1.25em;
			}
			#navPanel .link .indent-2 {
				display:inline-block;
				width:2.5em;
			}
			#navPanel .link .indent-3 {
				display:inline-block;
				width:3.75em;
			}
			#navPanel .link .indent-4 {
				display:inline-block;
				width:5em;
			}
			#navPanel .link .indent-5 {
				display:inline-block;
				width:6.25em;
			}
			#navPanel .close {
				text-decoration:none;
				-moz-transition:color 0.2s ease-in-out;
				-webkit-transition:color 0.2s ease-in-out;
				-ms-transition:color 0.2s ease-in-out;
				transition:color 0.2s ease-in-out;
				-webkit-tap-highlight-color:transparent;
				border:0;
				color:#cdcdcd;
				cursor:pointer;
				display:block;
				height:4em;
				padding-right:1.25em;
				position:absolute;
				right:0;
				text-align:right;
				top:0;
				vertical-align:middle;
				width:5em;
			}
			#navPanel .close:before {
				-moz-osx-font-smoothing:grayscale;
				-webkit-font-smoothing:antialiased;
				font-family:FontAwesome;
				font-style:normal;
				font-weight:normal;
				text-transform:none !important;
			}
			#navPanel .close:before {
				content:'\f00d';
				width:3em;
				height:3em;
				line-height:3em;
				display:block;
				position:absolute;
				right:0;
				top:0;
				text-align:center;
			}
			#navPanel .close:hover {
				color:inherit;
			}
			@media screen and (max-width:736px) {
				#navPanel .close {
					height:4em;
					line-height:4em;
				}
			}#navPanel.visible {
				-moz-transform:translateX(0);
				-webkit-transform:translateX(0);
				-ms-transform:translateX(0);
				transform:translateX(0);
				box-shadow:0.075em 0 0.25em 0 rgba(0,0,0,0.1);
				visibility:visible;
			}
			@media screen and (max-width:980px) {
				#navPanel {
					display:block;
				}
			}@media screen and (max-width:736px) {
				#navPanel {
					display:block;
				}
				#navPanel nav {
					padding:2.25em 1.25em;
				}
			}#navButton {
				display:none;
				height:4em;
				left:0;
				position:fixed;
				top:0;
				width:6em;
				z-index:10001;
			}
			#navButton .toggle {
				text-decoration:none;
				height:100%;
				left:0;
				position:absolute;
				top:0;
				width:100%;
				outline:0;
				border:0;
			}
			#navButton .toggle:before {
				-moz-osx-font-smoothing:grayscale;
				-webkit-font-smoothing:antialiased;
				font-family:FontAwesome;
				font-style:normal;
				font-weight:normal;
				text-transform:none !important;
			}
			#navButton .toggle:before {
				background:rgba(103,107,113,0.5);
				border-radius:4px;
				color:#fff;
				content:'\f0c9';
				display:block;
				font-size:16px;
				height:2.25em;
				left:0.5em;
				line-height:2.25em;
				position:absolute;
				text-align:center;
				top:0.5em;
				width:3.5em;
			}
			@media screen and (max-width:980px) {
				#navButton {
					display:block;
				}
			</style>
		</head>
		<body>
			<div id="header" class="alt">
				<a class="logo" href="index.html">
					<strong>
						Elemental
					</strong>
					by Pixelarity
				</a>
				<nav id="nav">
					<ul>
						<li class="active">
							<a href="index.html">
								Home
							</a>
						</li>
						<li>
							<a href="#">
								Dropdown
							</a>
							<ul>
								<li>
									<a href="#">
										Link One
									</a>
								</li>
								<li>
									<a href="#">
										Link Two
									</a>
								</li>
								<li>
									<a href="#">
										Link Three
									</a>
								</li>
								<li>
									<a href="#">
										Submenu
									</a>
									<ul>
										<li>
											<a href="#">
												Link One
											</a>
										</li>
										<li>
											<a href="#">
												Link Two
											</a>
										</li>
										<li>
											<a href="#">
												Link Three
											</a>
										</li>
										<li>
											<a href="#">
												Link Four
											</a>
										</li>
									</ul>
								</li>
								<li>
									<a href="#">
										Link Five
									</a>
								</li>
							</ul>
						</li>
						<li>
							<a href="generic.html">
								Generic
							</a>
						</li>
						<li>
							<a href="elements.html">
								Elements
							</a>
						</li>
					</ul>
				</nav>
			</div>
			<div id="main">
				<header>
					<span class="logo">
					</span>
					<h1>
						<strong>
							Elemental
						</strong>
						by Pixelarity
					</h1>
				</header>
				<section class="banner">
					<h2>
						Ipsum feugiat tempus sed aliquam
						<br />
						neque dapibus lorem volutpat
					</h2>
					<ul class="actions">
						<li>
							<a href="#" class="button">
								Learn More
							</a>
						</li>
					</ul>
				</section>
				<section id="one" class="wrapper spotlight">
					<div class="content">
						<div class="inner">
							<h2>
								Magna veroeros
							</h2>
							<p>
								Praesent dapibus, neque id cursus faucibus, tortor neque ege tas augue,
								eu vulputate magna eros eu erat. Aliquam erat et volutpat. Nam dui mi,
								tincidunt quis, accumsan porttitor tempus.
							</p>
							<ul class="actions">
								<li>
									<a href="#" class="button">
										Learn More
									</a>
								</li>
							</ul>
						</div>
					</div>
					<ul class="grid-icons">
						<li>
							<div class="inner">
								<span class="icon fa-cog major">
								</span>
								<h3>
									Sed tempus
								</h3>
							</div>
						</li>
						<li>
							<div class="inner">
								<span class="icon fa-desktop major">
								</span>
								<h3>
									Aenean lorem
								</h3>
							</div>
						</li>
						<li>
							<div class="inner">
								<span class="icon fa-signal major">
								</span>
								<h3>
									Felis aliquam
								</h3>
							</div>
						</li>
						<li>
							<div class="inner">
								<span class="icon fa-check major">
								</span>
								<h3>
									Dui volutpat
								</h3>
							</div>
						</li>
					</ul>
				</section>
				<section id="two" class="wrapper style1 spotlight alt">
					<div class="content">
						<div class="inner">
							<h2>
								Ipsum accumsan
							</h2>
							<p>
								Praesent dapibus, neque id cursus faucibus, tortor neque ege tas augue,
								eu vulputate magna eros eu erat. Aliquam erat et volutpat. Nam dui mi,
								tincidunt quis, accumsan porttitor tempus.
							</p>
							<ul class="actions">
								<li>
									<a href="#" class="button">
										Learn More
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="image">
						<img src="images/pic02.jpg" data-position="30% 30%" alt="" />
					</div>
				</section>
				<section id="three" class="wrapper style2 spotlight">
					<div class="content">
						<div class="inner">
							<h2>
								Etiam consequat
							</h2>
							<p>
								Praesent dapibus, neque id cursus faucibus, tortor neque ege tas augue,
								eu vulputate magna eros eu erat. Aliquam erat et volutpat. Nam dui mi,
								tincidunt quis, accumsan porttitor tempus.
							</p>
							<ul class="actions">
								<li>
									<a href="#" class="button">
										Learn More
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="image">
						<img src="images/pic03.jpg" data-position="center right" alt="" />
					</div>
				</section>
				<section id="four" class="wrapper style3 spotlight alt">
					<div class="content">
						<div class="inner">
							<h2>
								Quis adipiscing
							</h2>
							<p>
								Praesent dapibus, neque id cursus faucibus, tortor neque ege tas augue,
								eu vulputate magna eros eu erat. Aliquam erat et volutpat. Nam dui mi,
								tincidunt quis, accumsan porttitor tempus.
							</p>
							<ul class="actions">
								<li>
									<a href="#" class="button">
										Learn More
									</a>
								</li>
							</ul>
						</div>
					</div>
					<div class="image">
						<img src="images/pic04.jpg" data-position="top right" alt="" />
					</div>
				</section>
				<section id="five" class="wrapper special">
					<h2>
						Accumsan praesent
					</h2>
					<p>
						Praesent dapibus, neque id cursus faucibus, tortor neque ege tas augue,
						eu vulputate magna eros eu erat. Aliquam erat et volutpat. Nam dui mi,
						tincidunt quis, accumsan porttitor tempus lorem ipsum dolor sit amet consequat.
					</p>
					<ul class="faces">
						<li>
							<span class="image">
								<img src="images/pic05.jpg" alt="" />
							</span>
							<h3>
								Jane Doe
							</h3>
							<p>
								"Aliquam erat et volutpat. Nam dui mi, tincidunt quis, porttitor magna
								etiam lorem tempus."
							</p>
						</li>
						<li>
							<span class="image">
								<img src="images/pic06.jpg" alt="" />
							</span>
							<h3>
								John Anderson
							</h3>
							<p>
								"Aliquam erat et volutpat. Nam dui mi, tincidunt quis, porttitor magna
								etiam lorem tempus."
							</p>
						</li>
						<li>
							<span class="image">
								<img src="images/pic07.jpg" alt="" />
							</span>
							<h3>
								Kate Smith
							</h3>
							<p>
								"Aliquam erat et volutpat. Nam dui mi, tincidunt quis, porttitor magna
								etiam lorem tempus."
							</p>
						</li>
					</ul>
				</section>
			</div>
			<div id="footer">
				<h2>
					Get in touch
				</h2>
				<p>
					Praesent dapibus, neque id cursus faucibus, tortor neque ege tas augue,
					eu vulputate magna eros eu erat. Aliquam erat et volutpat. Nam dui mi,
					tincidunt quis, accumsan porttitor tempus lorem ipsum dolor sit amet consequat.
				</p>
				<ul class="major-icons">
					<li>
						<h3 class="icon fa-phone major">
							<span class="label">
								Phone
							</span>
						</h3>
						<p>
							(000) 000-0000
						</p>
					</li>
					<li>
						<h3 class="icon fa-map major">
							<span class="label">
								Address
							</span>
						</h3>
						<p>
							1234 Fictional Road
							<br />
							Nashville, TN 00000
						</p>
					</li>
					<li>
						<h3 class="icon fa-envelope major">
							<span class="label">
								Email
							</span>
						</h3>
						<p>
							<a href="#">
								info@untitled.tld
							</a>
						</p>
					</li>
				</ul>
				<ul class="joined-icons">
					<li>
						<a href="#" class="icon fa-facebook">
							<span class="label">
								Facebook
							</span>
						</a>
					</li>
					<li>
						<a href="#" class="icon fa-twitter">
							<span class="label">
								Twitter
							</span>
						</a>
					</li>
					<li>
						<a href="#" class="icon fa-github">
							<span class="label">
								GitHub
							</span>
						</a>
					</li>
					<li>
						<a href="#" class="icon fa-instagram">
							<span class="label">
								Instagram
							</span>
						</a>
					</li>
					<li>
						<a href="#" class="icon fa-linkedin">
							<span class="label">
								LinkedIn
							</span>
						</a>
					</li>
				</ul>
				<p class="copyright">
					&copy; Untitled. All rights reserved. Lorem ipsum dolor sit amet.
				</p>
			</div>
			<script>
				(function($){
					skel.breakpoints({xlarge: '(max-width: 1680px)', large: '(max-width: 1280px)', medium: '(max-width: 980px)', small: '(max-width: 736px)', xsmall: '(max-width: 480px)', xxsmall: '(max-width: 360px)'});
					$(function(){
						var $window = $(window), $body = $('body'), $header = $('#header'), $main = $('#main');
						$body.addClass('is-loading');
						$window.on('load', function(){
							window.setTimeout(function(){
								$body.removeClass('is-loading');
							}, 100);
						});
						$('form').placeholder();
						skel.on('+medium -medium', function(){
							$.prioritize('.important\\28 medium\\29', skel.breakpoint('medium').active);
						});
						$('#nav > ul').dropotron({alignment: 'center', openerActiveClass: 'dropotron-active'});
						if ( skel.vars.IEVersion < 9 )
							$header.removeClass('alt');
						if ( $header.hasClass('alt') ){
							$window.on('resize', function(){
								$window.trigger('scroll');
							});
							$main.scrollex({mode: 'top', top: '40vh', enter: function(){
									$header.removeClass('alt');
								}, leave: function(){
									$header.addClass('alt');
									$header.addClass('reveal');
								}});
						}
						$('<div id="navButton">' + '<a href="#navPanel" class="toggle"></a>' + '</div>').appendTo($body);
						$('<div id="navPanel">' + '<nav>' + $('#nav').navList() + '</nav>' + '<a href="#navPanel" class="close"></a>' + '</div>').appendTo($body).panel({delay: 500, hideOnClick: true, hideOnSwipe: true, resetScroll: true, resetForms: true, side: 'left'});
						if ( skel.vars.os == 'wp' && skel.vars.osVersion < 10 )
							$('#navPanel').css('transition', 'none');
						$('.spotlight').each(function(){
							var $this = $(this), $image = $this.find('.image'), $img = $image.find('img'), x;
							if ( $image.length == 0 )
								return;
							$image.css('background-image', 'url(' + $img.attr('src') + ')');
							if ( x = $img.data('position') )
								$image.css('background-position', x);
							$img.hide();
						});
					});
				})(jQuery);
			</script>
		</body>
	</html>