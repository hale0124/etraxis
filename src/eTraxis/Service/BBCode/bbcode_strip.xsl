<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="xml" version="1.0" encoding="UTF-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>

    <xsl:template match="bbcode">
        <bbcode>
            <xsl:apply-templates/>
        </bbcode>
    </xsl:template>

    <xsl:template match="bbcode_b|bbcode_i|bbcode_u|bbcode_s|bbcode_sub|bbcode_sup|bbcode_color|bbcode_size|bbcode_font|bbcode_align|bbcode_h1|bbcode_h2|bbcode_h3|bbcode_h4|bbcode_h5|bbcode_h6|bbcode_list|bbcode_ulist|bbcode_li|bbcode_url|bbcode_mail|bbcode_quote|bbcode_search">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="bbcode_code">
        <xsl:value-of select="."/>
    </xsl:template>

</xsl:stylesheet>
