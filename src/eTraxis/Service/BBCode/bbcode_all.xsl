<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output method="xml" version="1.0" encoding="UTF-8" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>

    <xsl:template match="bbcode">
        <bbcode>
            <xsl:apply-templates/>
        </bbcode>
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

    <xsl:template match="bbcode_size|bbcode_font|bbcode_align">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="bbcode_h1">
        <h1>
            <xsl:apply-templates/>
        </h1>
    </xsl:template>

    <xsl:template match="bbcode_h2">
        <h2>
            <xsl:apply-templates/>
        </h2>
    </xsl:template>

    <xsl:template match="bbcode_h3">
        <h3>
            <xsl:apply-templates/>
        </h3>
    </xsl:template>

    <xsl:template match="bbcode_h4">
        <h4>
            <xsl:apply-templates/>
        </h4>
    </xsl:template>

    <xsl:template match="bbcode_h5">
        <h5>
            <xsl:apply-templates/>
        </h5>
    </xsl:template>

    <xsl:template match="bbcode_h6">
        <h6>
            <xsl:apply-templates/>
        </h6>
    </xsl:template>

    <xsl:template match="bbcode_list">
        <ol>
            <xsl:apply-templates/>
        </ol>
    </xsl:template>

    <xsl:template match="bbcode_ulist">
        <ul>
            <xsl:apply-templates/>
        </ul>
    </xsl:template>

    <xsl:template match="bbcode_li">
        <li>
            <xsl:apply-templates/>
        </li>
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

    <xsl:template match="bbcode_quote">
        <blockquote class="bbcode">
            <xsl:apply-templates/>
        </blockquote>
    </xsl:template>

    <xsl:template match="bbcode_code">
        <pre class="bbcode">
            <xsl:value-of select="."/>
        </pre>
    </xsl:template>

    <xsl:template match="bbcode_search">
        <span class="search">
            <xsl:apply-templates/>
        </span>
    </xsl:template>

</xsl:stylesheet>
