<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="text" indent="no"/>
	<xsl:template match="/">
		<xsl:apply-templates />
	</xsl:template >
	<xsl:template match="REQUETES">
		<xsl:variable name="num"><xsl:value-of select ="position()+2"/></xsl:variable>
		<xsl:variable name="observer"><xsl:value-of select="/SAVE/node()[position()=$num]/REPONSE/@observer"/></xsl:variable>
		<xsl:for-each select="REQUETE[2]">
			<xsl:variable name="xferobserver">
			<xsl:choose>
			    <xsl:when test="$observer='Core.Custom'">Xfer_Container_Custom</xsl:when>
			    <xsl:when test="$observer='Core.Acknowledge'">Xfer_Container_Acknowledge</xsl:when>
			    <xsl:when test="$observer='Core.DialogBox'">Xfer_Container_DialogBox</xsl:when>
			    <xsl:when test="$observer='CORE.Exception'">Xfer_Container_Exception</xsl:when>
			    <xsl:when test="$observer='CORE.Menu'">Xfer_Container_Menu</xsl:when>
			    <xsl:when test="$observer='Core.Print'">Xfer_Container_Print</xsl:when>
			    <xsl:when test="$observer='Core.Template'">Xfer_Container_Template</xsl:when>
			    <xsl:otherwise>???</xsl:otherwise>
			</xsl:choose>
			</xsl:variable>
			<xsl:text>$rep=$test->CallAction("</xsl:text>
			<xsl:value-of select='@extension'/><xsl:text>","</xsl:text><xsl:value-of select='@action'/>
			<xsl:text>",array(</xsl:text><xsl:for-each select="PARAM"><xsl:text>"</xsl:text><xsl:value-of select='@name'/><xsl:text>"=>"</xsl:text><xsl:value-of select='text()'/><xsl:text>",</xsl:text></xsl:for-each>
			<xsl:text>),"</xsl:text><xsl:value-of select='$xferobserver'/><xsl:text>");
</xsl:text>
			<xsl:for-each select="/SAVE/node()[position()=$num]/REPONSE">
				<xsl:choose>
				    <xsl:when test="$observer='Core.Custom'"><xsl:call-template name='Xfer_Container_Custom'/></xsl:when>
				    <xsl:when test="$observer='Core.Acknowledge'"><xsl:call-template name='Xfer_Container_Acknowledge'/></xsl:when>
				    <xsl:when test="$observer='Core.DialogBox'"><xsl:call-template name='Xfer_Container_DialogBox'/></xsl:when>
				    <xsl:when test="$observer='CORE.Exception'"><xsl:call-template name='Xfer_Container_Exception'/></xsl:when>
				    <xsl:when test="$observer='CORE.Menu'"><xsl:call-template name='Xfer_Container_Menu'/></xsl:when>
				    <xsl:when test="$observer='Core.Print'"><xsl:call-template name='Xfer_Container_Print'/></xsl:when>
				    <xsl:when test="$observer='Core.Template'"><xsl:call-template name='Xfer_Container_Template'/></xsl:when>
				</xsl:choose>
			</xsl:for-each>
		</xsl:for-each>
	</xsl:template >
	<xsl:template match="REPONSES">
	</xsl:template >
	
	<xsl:template name='Xfer_Container_Custom'>
		<xsl:text>$test->assertEquals(</xsl:text>
		<xsl:value-of select="count(ACTIONS/*)"/>
		<xsl:text>,COUNT($rep->m_actions),'nb action');
</xsl:text>
		<xsl:for-each select="ACTIONS/ACTION">
			<xsl:text>$act=$rep->m_actions[</xsl:text><xsl:value-of select="position()-1"/><xsl:text>];
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="text()"/><xsl:text>",$act->m_title,'Titre action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="@extension"/><xsl:text>",$act->m_extension,'Ext action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="@action"/><xsl:text>",$act->m_action,'Act action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
		</xsl:for-each>
		<xsl:text>$test->assertEquals(</xsl:text>
		<xsl:value-of select="count(COMPONENTS/*)"/>
		<xsl:text>,$rep->getComponentCount(),'nb component');
</xsl:text>
		<xsl:for-each select="COMPONENTS/*">
			<xsl:if test="not(@name='' or substring(@name,1,5)='label' or substring(@name,1,3)='lbl')">
			<xsl:text>//</xsl:text><xsl:value-of select="name()"/> - <xsl:value-of select="@name"/><xsl:text>
</xsl:text>
			<xsl:text>$comp=$rep->getComponents('</xsl:text><xsl:value-of select="normalize-space(@name)"/><xsl:text>');
</xsl:text>
			<xsl:apply-templates select='.' />
			</xsl:if>
		</xsl:for-each>

	</xsl:template >
	
	<xsl:template name='Xfer_Container_Acknowledge'>
		<xsl:for-each select="ACTION">
			<xsl:text>$act=$rep->m_action;
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="@extension"/><xsl:text>",$act->m_extension,'Ext action');
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="@action"/><xsl:text>",$act->m_action,'Act action');
</xsl:text>
		</xsl:for-each>
	</xsl:template >
	
	<xsl:template name='Xfer_Container_DialogBox'>
		<xsl:text>$test->assertEquals(</xsl:text><xsl:value-of select="TEXT/@type"/><xsl:text>,$rep->m_type,'Type dialogue');
</xsl:text>
		<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="TEXT/text()"/><xsl:text>",$rep->m_text,'Text dialogue');
</xsl:text>
		<xsl:for-each select="ACTIONS/ACTION">
			<xsl:text>$act=$rep->m_actions[</xsl:text><xsl:value-of select="position()-1"/><xsl:text>];
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="text()"/><xsl:text>",$act->m_title,'Titre action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="@extension"/><xsl:text>",$act->m_extension,'Ext action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="@action"/><xsl:text>",$act->m_action,'Act action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
		</xsl:for-each>
	</xsl:template >
	
	<xsl:template name='Xfer_Container_Exception'>
			<xsl:text>$error=$rep->m_error;
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="EXCEPTION/MESSAGE/text()"/><xsl:text>",$error->getMessage(),'Message erreur');
</xsl:text>
			<xsl:text>$test->assertEquals(</xsl:text><xsl:value-of select="EXCEPTION/CODE/text()"/><xsl:text>,$error->getCode(),'Code erreur');
</xsl:text>
	</xsl:template >
	
	<xsl:template name='Xfer_Container_Menu'>
	</xsl:template >
	
	<xsl:template name='Xfer_Container_Print'>
	</xsl:template >
	
	<xsl:template name='Xfer_Container_Template'>
	</xsl:template >


	<xsl:template name="COMP">
		<xsl:param name="class"/>
		<xsl:text>$test->assertClass("</xsl:text><xsl:value-of select="$class"/><xsl:text>",$comp,"Classe de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
		<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="normalize-space(text())"/><xsl:text>","".$comp->m_value,"Valeur de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
	</xsl:template >

	<xsl:template match="TAB">
	</xsl:template>

	<xsl:template match="LABEL">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Label</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="IMAGE">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Image</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="LINK">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_LinkLabel</xsl:with-param></xsl:call-template>
		<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="LINK/text()"/><xsl:text>",$comp->Link,"Liens de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
	</xsl:template>

	<xsl:template match="LABELFORM">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_LabelForm</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="EDIT">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Edit</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="DATE">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Date</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="TIME">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Time</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="DATETIME">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_DateTime</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="PASSWD">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Passwd</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="MEMO">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Memo</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="MEMOFORM">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_MemoForm</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="FLOAT">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Float</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="UPLOAD">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_UpLoad</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="DOWNLOAD">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_DownLoad</xsl:with-param></xsl:call-template>
	</xsl:template>

	<xsl:template match="BUTTON">
		<xsl:text>$test->assertClass("Xfer_Comp_Button",$comp,"Classe de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
		<xsl:text>$act=$comp->m_action;
</xsl:text>
		<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="ACTIONS/ACTION/text()"/><xsl:text>",$act->m_title,'Titre action btn');
</xsl:text>
		<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="ACTIONS/ACTION/@extension"/><xsl:text>",$act->m_extension,'Ext action btn');
</xsl:text>
		<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="ACTIONS/ACTION/@action"/><xsl:text>",$act->m_action,'Act action btn');
</xsl:text>
	</xsl:template>

	<xsl:template match="CHECK">
		<xsl:text>$test->assertClass("Xfer_Comp_Check",$comp,"Classe de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
		<xsl:text>$test->assertEquals(</xsl:text>
		<xsl:choose>
			<xsl:when test="text()='1'">true</xsl:when>
			<xsl:otherwise>false</xsl:otherwise>
		</xsl:choose>
		<xsl:text>,$comp->m_value,"Valeur de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
	</xsl:template>

	<xsl:template match="SELECT">
		<xsl:call-template name='COMP'><xsl:with-param name="class">Xfer_Comp_Select</xsl:with-param></xsl:call-template>
		<xsl:text>$test->assertEquals(</xsl:text>
		<xsl:value-of select="count(CASE)"/>
		<xsl:text>,COUNT($comp->m_select),'Nb select de </xsl:text><xsl:value-of select="@name"/><xsl:text>');
</xsl:text>
	</xsl:template>

	<xsl:template match="CHECKLIST">
		<xsl:text>$test->assertClass("Xfer_Comp_CheckList",$comp,"Classe de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
		<xsl:text>$test->assertEquals(</xsl:text>
		<xsl:value-of select="count(CASE)"/>
		<xsl:text>,COUNT($comp->m_select),'Nb check de </xsl:text><xsl:value-of select="@name"/><xsl:text>');
</xsl:text>
	</xsl:template>

	<xsl:template match="GRID">
		<xsl:text>$test->assertEquals(</xsl:text><xsl:value-of select="count(ACTIONS/ACTION)"/><xsl:text>,count($comp->m_actions),"Nb grid actions de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
		<xsl:text>$test->assertEquals(</xsl:text><xsl:value-of select="count(HEADER)"/><xsl:text>,count($comp->m_headers),"Nb grid headers de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
		<xsl:text>$test->assertEquals(</xsl:text><xsl:value-of select="count(RECORD)"/><xsl:text>,count($comp->m_records),"Nb grid records de </xsl:text><xsl:value-of select="@name"/><xsl:text>");
</xsl:text>
		<xsl:for-each select="ACTIONS/ACTION">
			<xsl:text>$act=$comp->m_actions[</xsl:text><xsl:value-of select="position()-1"/><xsl:text>];
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="text()"/><xsl:text>",$act->m_title,'Titre grid action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="@extension"/><xsl:text>",$act->m_extension,'Ext grid action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="@action"/><xsl:text>",$act->m_action,'Act grid action #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
		</xsl:for-each>
		<xsl:text>$headers=$comp->m_headers;
</xsl:text>
		<xsl:for-each select="HEADER">
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="text()"/><xsl:text>",$headers["</xsl:text><xsl:value-of select="@name"/><xsl:text>"]->m_descript,'Header #</xsl:text><xsl:value-of select="position()"/><xsl:text>');
</xsl:text>
		</xsl:for-each>
		<xsl:for-each select="RECORD">
			<xsl:variable name="id"><xsl:value-of select ="@id"/></xsl:variable>
			<xsl:text>$rec=$comp->m_records[</xsl:text><xsl:value-of select="$id"/><xsl:text>];
</xsl:text>
			<xsl:for-each select="VALUE">
			<xsl:text>$test->assertEquals("</xsl:text><xsl:value-of select="text()"/><xsl:text>",$rec["</xsl:text><xsl:value-of select="@name"/><xsl:text>"],"Valeur de grid [</xsl:text><xsl:value-of select="$id"/><xsl:text>,</xsl:text><xsl:value-of select="@name"/><xsl:text>]");
</xsl:text>
			</xsl:for-each>
		</xsl:for-each>
	</xsl:template>

      
</xsl:stylesheet> 
