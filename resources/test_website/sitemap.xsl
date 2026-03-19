<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <xsl:output method="html" encoding="UTF-8" indent="yes"/>

  <xsl:template match="/">
    <html>
      <head>
        <title>Sitemap - Dr. Aravind's IVF</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; background: #fafafa; color: #333; }
          h1 { color: #812574; }
          table { width: 100%; border-collapse: collapse; margin-top: 20px; }
          th, td { border: 1px solid #ddd; padding: 10px; }
          th { background: #812574; color: #fff; text-align: left; }
          tr:nth-child(even) { background: #f9f0f9; }
          a { color: #812574; text-decoration: none; font-weight: bold; }
          a:hover { text-decoration: underline; }
        </style>
      </head>
      <body>
        <h1>Sitemap for Dr. Aravind's IVF</h1>
        <table>
          <tr><th>URL</th><th>Last Modified</th><th>Priority</th></tr>
          <xsl:for-each select="urlset/url">
            <tr>
              <td><a href="{loc}"><xsl:value-of select="loc"/></a></td>
              <td><xsl:value-of select="lastmod"/></td>
              <td><xsl:value-of select="priority"/></td>
            </tr>
          </xsl:for-each>
        </table>
      </body>
    </html>
  </xsl:template>
</xsl:stylesheet>
