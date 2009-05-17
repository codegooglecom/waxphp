
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"> 
 <xsl:template match="/"> 
    <html>
        <head>
            <title><xsl:value-of select="page/info/title" /></title>
            <xsl:for-each select="page/css">
                <link rel="stylesheet" type='text/css' href="{href}" />
            </xsl:for-each>
            <xsl:for-each select="page/scripts">
                <script type='text/javascript' language="JavaScript" src="{src}"></script>
            </xsl:for-each>
            <xsl:for-each select="page/meta">
                <meta http-equiv="{name}" content="{value}" />
            </xsl:for-each>
        </head>
        <body>
            <xsl:value-of select="page/content/content" />
        </body>
    </html>
</xsl:template>
</xsl:stylesheet>