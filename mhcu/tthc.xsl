<?xml version="1.0"?>
<xsl:stylesheet version="2.0"
      xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
<xsl:template match="/">
<HTML>
<head>
		<link type="text/css" href="style.css" rel="stylesheet"/>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>		
		<script language="javascript">var timer;</script>
		<script src="jquery.js"></script>
        <script src="jquery.overscroll.js"></script>     
        <script>
            $(function(o){	
                o = $("#base2").overscroll();
            });				
        </script>
        <script type="text/javascript" src="autoHeight.js"></script>
		<script src="function.js"></script>
        <script language="javascript">
			$(document).ready(function(){
			var height1 = $(document).height(); // height of full document
			//var height2 = $("body").height(); // height of header
			var height = height1  + "px";
			document.getElementById('base2').style.height = height1;
			//document.getElementById('base').style.height = height1; AC_FL_RunContent
			});
		</script>
        <script src="Scripts/AC_RunActiveContent.js" type="text/javascript"></script>
</head>
<div id="base3">
<style type="text/css">
	.S3{
		background-color:#fefce6; 
		height:35px; 
		font-family:"Times New Roman", Times, serif; 
		font-size:21px; color:#5a0101; 
		font-weight:none; 
		border:none;
		border-left:1px #b06c0a solid; 
		border-bottom:1px #b06c0a solid; 
		padding-left:7px;
		cursor:pointer;	}
	.S4{
		background-color:#fff9b8; 
		height:35px; 
		font-family:"Times New Roman", Times, serif; 
		font-size:21px; color:#aa0000; 
		font-weight:none; 
		border:none;
		border-left:1px #b06c0a solid; 
		border-bottom:1px #b06c0a solid; 
		padding-left:7px;
		cursor:pointer;		 	}
</style>
<table width="1280px" border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td colspan="3" valign="top" align="center">
			<img src="images/banner.jpg" style="border-right:0px solid #999999" width="1280px" height="104px"/>		
		</td>
	</tr>
</table>
<Table  cellspacing="0" cellpadding="0"  align="center" border="0" width="1280px" class="bd_top_menu"  bgcolor="#ffedc1">
	<tr>
		<td id="PrentTaichinhID" onclick="ShowHide('PrentTaichinhID');" class="parent" align="center" ><a href="#">TÀI CHÍNH KẾ HOẠCH</a></td>
		<td id="PrentXaydungID" onclick="ShowHide('PrentXaydungID');" class="parent" align="center" ><a href="#">CẤP PHÉP XÂY DỰNG</a></td>
		<td id="PrentTainguyenID" onclick="ShowHide('PrentTainguyenID');" class="parent" align="center" ><a href="#">TÀI NGUYÊN MÔI TRƯỜNG</a></td>
		<td id="PrentTuphapID" onclick="ShowHide('PrentTuphapID');" class="parent" align="center" ><a href="#">TƯ PHÁP HỘ TỊCH</a></td>
		<td id="PrentChinhsachID" onclick="ShowHide('PrentChinhsachID');" class="parent" align="center" ><a href="#">CHÍNH SÁCH XÃ HỘI</a></td>
	</tr>
</Table>
</div>
<body>
<div id="base2">
<div id="menu">				 					  
<xsl:call-template name="thu_tuc_hanh_chinh" /></div>
<div id="base" ></div>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="1280" class="bg_center">
	<tr>
		<td  align="center">
       <iframe frameborder="0" class="autoHeight" id="noi_dung_tthc" src="html/00.htm" width="100%" scrolling="no" />
		<!--<IFRAME frameborder="0" SRC="html/02.htm" WIDTH="100%" HEIGHT="2700px" id="noi_dung_tthc" scrolling="no"> </IFRAME>-->
	</td>
	</tr>
</table>
<script language="javascript">	
	function HideLoad(){
		hide_row('ChildTaichinhID');		
		hide_row('ChildXaydungID');
		hide_row('ChildTainguyenID');
		hide_row('ChildTuphapID');
		hide_row('ChildChinhsachID');
		document.getElementById('noi_dung_tthc').src = "html/00.htm";					
	}
	HideLoad();
</script>
<script language="javascript">
	function refesh() {
		var t=setInterval("location.reload(true)",200000);
	}
</script>
</div>
</body>
</HTML>
</xsl:template>
<xsl:template name="thu_tuc_hanh_chinh">
<script language="javascript">
	function HideLoad1(){
		hide_row('ChildTaichinhID');		
		hide_row('ChildXaydungID');
		hide_row('ChildTainguyenID');
		hide_row('ChildTuphapID');
		hide_row('ChildChinhsachID');	
	}	
</script>
<script language="javascript">
	function HideLoad2(){
		document.getElementById('noi_dung_tthc').src = "html/000.htm";			
	}
</script>
<table width="1280px" align="center" border="0">
		<tr>
			<td id="ChildTaichinhID" name="ChildTaichinhID">
				<table cellpadding="0" cellspacing="0" width="100%" align="center" class="boder">
					<tr>
						<td align="center" width="5%" class="swap_top">Stt </td>
						<td align="center" class="swap_top" colspan="2">Thủ tục hành chính</td>
					</tr>
					 <xsl:for-each select="root/root_taichinh/item">			
						<xsl:sort select="@order" data-type="number" order="ascending"/>		        									
						<tr class="mau_nen" onmouseover="this.className='S4'" onmouseout="this.className='S3'">
							<td align="center" class="swap_bottom_right" >
								<xsl:value-of select="@order"/>
							</td>
							<td class="swap_bottom_right">
								<xsl:value-of select="@name"/>
							</td>
                             <td width="10%" class="swap_bottom_right" align="center" id="td_menu" name="td_menu" >
							 	<xsl:attribute name="onclick">
									DoLink('<xsl:value-of select="@order"/>','<xsl:value-of select="@link"/>')								  
								</xsl:attribute>
								<img src="images/Untitled-1.png" alt="Xem chi tiet" align="middle" style="cursor:pointer;" />
							 </td>				
						</tr>						
					 </xsl:for-each>
				</table>
			</td>			
		</tr>
		<tr>
			<td id="ChildXaydungID" name="ChildXaydungID">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" class="boder">	
						<tr>
							<td align="center" width="5%" class="swap_top">Stt </td>
							<td align="center" class="swap_top" colspan="2">Thủ tục hành chính</td>
						</tr>
						 <xsl:for-each select="root/root_xaydung/item">			
							<xsl:sort select="@order" data-type="number" order="ascending"/>		        									
							<tr class="mau_nen" onmouseover="this.className='S4'" onmouseout="this.className='S3'">		
								<td align="center" class="swap_bottom_right">
									<xsl:value-of select="@order"/>
								</td>					          
								<td class="swap_bottom_right">
								<xsl:value-of select="@name"/>
							</td>
                             <td width="10%" class="swap_bottom_right" align="center" id="td_menu" name="td_menu">
							 	<xsl:attribute name="onclick">
									DoLink('<xsl:value-of select="@order"/>','<xsl:value-of select="@link"/>')								  
								</xsl:attribute>
								<img src="images/Untitled-1.png" alt="Xem chi tiet" align="middle" style="cursor:pointer;" />
							 </td>		
							</tr>						
						</xsl:for-each>
				</table>
			 </td>
		  </tr>
		  <tr>
			<td id="ChildTainguyenID" name="ChildTainguyenID">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" class="boder">	
						<tr>
							<td align="center" width="5%" class="swap_top">Stt </td>
							<td align="center" class="swap_top" colspan="2">Thủ tục hành chính</td>
						</tr>
						 <xsl:for-each select="root/root_tainguyen/item">			
							<xsl:sort select="@order" data-type="number" order="ascending"/>		        									
							<tr class="mau_nen" onmouseover="this.className='S4'" onmouseout="this.className='S3'">		
								<td align="center" class="swap_bottom_right">
									<xsl:value-of select="@order"/>
								</td>					          
								<td class="swap_bottom_right">
								<xsl:value-of select="@name"/>
							</td>
                             <td width="10%" class="swap_bottom_right" align="center" id="td_menu" name="td_menu">
							 	<xsl:attribute name="onclick">
									DoLink('<xsl:value-of select="@order"/>','<xsl:value-of select="@link"/>')								  
								</xsl:attribute>
								<img src="images/Untitled-1.png" alt="Xem chi tiet" align="middle" style="cursor:pointer;"/>
							 </td>				
							</tr>						
						</xsl:for-each>
				</table>
			</td>
		  </tr>
		  <tr>
			<td id="ChildTuphapID" name="ChildTuphapID">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" class="boder">	
						<tr>
							<td align="center" width="5%" class="swap_top">Stt</td>
							<td  align="center" class="swap_top" colspan="2">Thủ tục hành chính</td>
						</tr>
						 <xsl:for-each select="root/root_tuphap/item">			
							<xsl:sort select="@order" data-type="number" order="ascending"/>		        									
							<tr class="mau_nen" onmouseover="this.className='S4'" onmouseout="this.className='S3'">		
								<td align="center" class="swap_bottom_right">
									<xsl:value-of select="@order"/>
								</td>					          
								<td class="swap_bottom_right">
								<xsl:value-of select="@name"/>
							</td>
                             <td width="10%" class="swap_bottom_right" align="center" id="td_menu" name="td_menu">
							 	<xsl:attribute name="onclick">
									DoLink('<xsl:value-of select="@order"/>','<xsl:value-of select="@link"/>')								  
								</xsl:attribute>
								<img src="images/Untitled-1.png" alt="Xem chi tiet" align="middle" style="cursor:pointer;"/>
							 </td>			
							</tr>						
						</xsl:for-each>
				</table>
			</td>
		  </tr>
		   <tr>
			<td id="ChildChinhsachID" name="ChildChinhsachID">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" align="center" class="boder">	
						<tr>
							<td align="center" width="5%" class="swap_top">Stt</td>
							<td  align="center" class="swap_top" colspan="2">Thủ tục hành chính</td>
						</tr>
						 <xsl:for-each select="root/root_chinhsach/item">			
							<xsl:sort select="@order" data-type="number" order="ascending"/>		        									
							<tr class="mau_nen" onmouseover="this.className='S4'" onmouseout="this.className='S3'">		
								<td align="center" class="swap_bottom_right">
									<xsl:value-of select="@order"/>
								</td>					          
								<td class="swap_bottom_right">
								<xsl:value-of select="@name"/>
							</td>
                             <td width="10%" class="swap_bottom_right" align="center" id="td_menu"  name="td_menu">
							 	<xsl:attribute name="onclick">
									DoLink('<xsl:value-of select="@order"/>','<xsl:value-of select="@link"/>')								  
								</xsl:attribute>
								<img src="images/Untitled-1.png" alt="Xem chi tiet" align="middle" style="cursor:pointer;"/>
							 </td>			
							</tr>						
						</xsl:for-each>
				</table>
			</td>
		  </tr>
</table>
</xsl:template>
</xsl:stylesheet>