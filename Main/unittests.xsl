<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:lxslt="http://xml.apache.org/xslt">
	<xsl:output method="text"/>
	
	<xsl:variable name="testsuite.list" select="//testsuite"/>
	<xsl:variable name="testsuite.error.count" select="count($testsuite.list/error)"/>
	<xsl:variable name="testcase.list" select="$testsuite.list/testcase"/>
	<xsl:variable name="testcase.error.list" select="$testcase.list/error"/>
	<xsl:variable name="testcase.failure.list" select="$testcase.list/failure"/>
	<xsl:variable name="totalErrorsAndFailures" select="count($testcase.error.list) + count($testcase.failure.list) + $testsuite.error.count"/>

	<xsl:template match="/" mode="unittests">
		{[center]}{[underline]}Tests unitaires:&#160;<xsl:value-of select="count($testcase.list)"/>{[/underline]}{[/center]}
		<xsl:choose>
			<xsl:when test="$totalErrorsAndFailures > 0">
				{[center]}{[italic]}Nombre d'erreurs:&#160;<xsl:value-of select="$totalErrorsAndFailures"/>{[/italic]}{[/center]}
				{[newline]}
			</xsl:when>
                	<xsl:when test="count($testsuite.list) = 0">
				{[italic]}Pas de tests{[/italic]}
				{[newline]}
				{[italic]}Ce projet n'a aucun test{[/italic]}
				{[newline]}
			</xsl:when>
			<xsl:when test="$totalErrorsAndFailures = 0">
				{[italic]}Succ&#233;s g&#233;n&#233;ral{[/italic]}
				{[newline]}
			</xsl:when>
		</xsl:choose>
		Temps d'execussion:
		<xsl:call-template name="showTime">
			<xsl:with-param name="value" select="//testsuite/@time"/>
		</xsl:call-template>
		{[newline]}

		<xsl:for-each select="//testsuite/testcase">
			<xsl:if test="not(./error)">
				<xsl:if test="not(./failure)">
					{[font color='green']}Succ&#233;s [<xsl:value-of select="@classname"/>]<xsl:value-of select="@name"/>{[/font]} - Dur&#233;e 
					<xsl:call-template name="showTime">
						<xsl:with-param name="value" select="@time"/>
					</xsl:call-template>
					{[newline]}
				</xsl:if>
			</xsl:if>
		</xsl:for-each>

		<xsl:apply-templates select="$testcase.error.list" mode="unittests"/>
                <xsl:apply-templates select="$testcase.failure.list" mode="unittests"/>

		<xsl:if test="$totalErrorsAndFailures > 0">
			{[newline]}
			{[hr]}
			{[center]}{[italic]}D&#233;tail des erreurs{[/italic]}{[/center]}
			<!-- (PENDING) Why doesn't this work if set up as variables up top? -->
			<xsl:call-template name="testdetail">
				<xsl:with-param name="detailnodes" select="//testsuite/testcase[.//error]"/>
			</xsl:call-template>
			<xsl:call-template name="testdetail">
				<xsl:with-param name="detailnodes" select="//testsuite/testcase[.//failure]"/>
			</xsl:call-template>
            	</xsl:if>
	</xsl:template>

	<!-- UnitTest Errors -->
	<xsl:template match="error" mode="unittests">
		{[font color='#ff8800']}Erreur [<xsl:value-of select="../@classname"/>]<xsl:value-of select="../@name"/>{[/font]} - Dur&#233;e 
		<xsl:call-template name="showTime">
			<xsl:with-param name="value" select="../@time"/>
		</xsl:call-template>
		{[newline]}
	</xsl:template>

	<!-- UnitTest Failures -->
	<xsl:template match="failure" mode="unittests">
		{[font color='red']}Echec [<xsl:value-of select="../@classname"/>]<xsl:value-of select="../@name"/>{[/font]} - Dur&#233;e 
		<xsl:call-template name="showTime">
			<xsl:with-param name="value" select="../@time"/>
		</xsl:call-template>
		{[newline]}
	</xsl:template>

	<xsl:template name="showTime">
		<xsl:param name="value"/>
		<xsl:variable name="hour" select="floor($value div 3600)"/>
		<xsl:variable name="min" select="floor(($value - ($hour*60)) div 60)"/>
		<xsl:variable name="sec" select="round($value - ($hour*3600) - ($min*60))"/>
		<xsl:if test="$hour>0">
			<xsl:value-of select="$hour"/> h 
		</xsl:if>
		<xsl:if test="$min>0">
			<xsl:value-of select="$min"/> min 
		</xsl:if>
		<xsl:value-of select="$sec"/> sec
	</xsl:template>

	<!-- UnitTest Errors And Failures Detail Template -->
	<xsl:template name="testdetail">
		<xsl:param name="detailnodes"/>
		<xsl:for-each select="$detailnodes">
                	{[underline]}[<xsl:value-of select="@classname"/>]<xsl:value-of select="@name"/>{[/underline]}{[newline]}
			<xsl:if test="error">
				{<xsl:value-of select="./error/@type"/>}:<xsl:value-of select="./error/@message"/>
				{[font size='-1']}
				<xsl:call-template name="br-replace">
					<xsl:with-param name="word" select="error"/>
					<xsl:with-param name="count" select="0"/>
				</xsl:call-template>
				{[/font]}
			</xsl:if>
			<xsl:if test="failure">
				{<xsl:value-of select="./failure/@type"/>}:<xsl:value-of select="./failure/@message"/>
				{[font size='-1']}
				<xsl:call-template name="br-replace">
					<xsl:with-param name="word" select="failure"/>
					<xsl:with-param name="count" select="0"/>
				</xsl:call-template>
				{[/font]}
			</xsl:if>
			{[newline]}{[newline]}
      		</xsl:for-each>
    	</xsl:template>

	<xsl:template name="br-replace">
		<xsl:param name="word"/>
		<xsl:param name="count"/>
		<xsl:variable name="stackstart"><xsl:text>#</xsl:text></xsl:variable>
		<xsl:choose>
			<xsl:when test="contains($word,$stackstart)">
				<xsl:call-template name="middle-replace">
					<xsl:with-param name="word" select="substring-before($word,$stackstart)"/>
				</xsl:call-template>
				<xsl:call-template name="br-replace">
					<xsl:with-param name="word" select="substring-after($word,$stackstart)"/>
					<xsl:with-param name="count" select="$count + 1"/>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:call-template name="middle-replace">
					<xsl:with-param name="word" select="$word"/>
				</xsl:call-template>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="middle-replace">
		<xsl:param name="word"/>
		<xsl:variable name="stackmiddle"><xsl:text>:</xsl:text></xsl:variable>
		<xsl:choose>
			<xsl:when test="contains($word,$stackmiddle)">
				<xsl:value-of select="substring-before($word,$stackmiddle)"/>{[newline]}
				&#160;&#160;&#160;&#160;&#160;<xsl:value-of select="substring-after($word,$stackmiddle)"/>{[newline]}
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$word"/>{[newline]}
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<xsl:template match="/">
		<xsl:apply-templates select="." mode="unittests"/>
	</xsl:template>
</xsl:stylesheet>
