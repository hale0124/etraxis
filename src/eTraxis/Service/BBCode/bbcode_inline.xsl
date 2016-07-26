<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="xml" version="1.0" encoding="UTF-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>

    <xsl:template match="bbcode">
        <bbcode>
            <xsl:apply-templates/>
        </bbcode>
    </xsl:template>

    <xsl:template match="bbcode_size|bbcode_font|bbcode_align|bbcode_h1|bbcode_h2|bbcode_h3|bbcode_h4|bbcode_h5|bbcode_h6|bbcode_list|bbcode_ulist|bbcode_li|bbcode_quote">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="bbcode_b">
        <b>
            <xsl:apply-templates/>
        </b>
    </xsl:template>

    <xsl:template match="bbcode_i">
        <i>
            <xsl:apply-templates/>
        </i>
    </xsl:template>

    <xsl:template match="bbcode_u">
        <u>
            <xsl:apply-templates/>
        </u>
    </xsl:template>

    <xsl:template match="bbcode_s">
        <s>
            <xsl:apply-templates/>
        </s>
    </xsl:template>

    <xsl:template match="bbcode_sub">
        <sub>
            <xsl:apply-templates/>
        </sub>
    </xsl:template>

    <xsl:template match="bbcode_sup">
        <sup>
            <xsl:apply-templates/>
        </sup>
    </xsl:template>

    <xsl:template match="bbcode_color">
        <span>
            <xsl:attribute name="style">
                <xsl:text>color:</xsl:text>
                <xsl:value-of select="@value"/>
            </xsl:attribute>
            <xsl:apply-templates/>
        </span>
    </xsl:template>

    <xsl:template match="bbcode_url">
        <xsl:if test="boolean(@value)">
            <a>
                <xsl:attribute name="href">
                    <xsl:value-of select="@value"/>
                </xsl:attribute>
                <xsl:apply-templates/>
            </a>
        </xsl:if>
        <xsl:if test="not(boolean(@value))">
            <a>
                <xsl:attribute name="href">
                    <xsl:value-of select="."/>
                </xsl:attribute>
                <xsl:value-of select="."/>
            </a>
        </xsl:if>
    </xsl:template>

    <xsl:template match="bbcode_mail">
        <xsl:if test="boolean(@value)">
            <a>
                <xsl:attribute name="href">
                    <xsl:text>mailto:</xsl:text>
                    <xsl:value-of select="@value"/>
                </xsl:attribute>
                <xsl:apply-templates/>
            </a>
        </xsl:if>
        <xsl:if test="not(boolean(@value))">
            <a>
                <xsl:attribute name="href">
                    <xsl:text>mailto:</xsl:text>
                    <xsl:value-of select="."/>
                </xsl:attribute>
                <xsl:value-of select="."/>
            </a>
        </xsl:if>
    </xsl:template>

    <xsl:template match="bbcode_search">
        <span class="search">
            <xsl:apply-templates/>
        </span>
    </xsl:template>

    <xsl:template match="bbcode_code">
        <xsl:value-of select="."/>
    </xsl:template>

</xsl:stylesheet>
