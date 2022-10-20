<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
				xmlns:xhtml="http://www.w3.org/1999/xhtml">
	<xsl:output method="html" indent="yes" encoding="UTF-8"/>
	<xsl:template match="/">
		<html lang="ru-RU">
		<head>
			<meta charset="UTF-8"/>
			<title>Карта сайта ЭнергоТеплоКомплект</title>
			<link rel="stylesheet" href="https://etk72.ru/media/com_jlsitemap/css/sitemap.min.css"/>
		</head>
		<body>
		<div class="container">
			<h1>
				Карта сайта ЭнергоТеплоКомплект			</h1>
			<p class="description">
				Карта сайта содержит <xsl:value-of select="count(sitemap:urlset/sitemap:url)"/> ссылок			</p>
			<xsl:apply-templates/>
			<div class="center muted">
				Карта сайта сгенирирована компонентом JL Sitemap			</div>
							<div class="center muted">
					2022-10-05 10:33:54				</div>
					</div>
		</body>
		</html>
	</xsl:template>
	<xsl:template match="sitemap:urlset">
		<table>
			<thead>
			<tr>
				<th class="center" width="1%">#</th>
				<th>Ссылка</th>
				<th>Частота изменений</th>
				<th>Приоритет</th>
				<th>Последние изменения</th>
			</tr>
			</thead>
			<tbody>
			<xsl:for-each select="sitemap:url">
				<xsl:variable name="loc">
					<xsl:value-of select="sitemap:loc"/>
				</xsl:variable>
				<tr>
					<td>
						<xsl:value-of select="position()"/>
					</td>
					<td>
						<div>
							<a href="{$loc}">
								<xsl:value-of select="sitemap:loc"/>
							</a>
						</div>
						<xsl:if test="xhtml:link">
							<div class="alternates">
								<xsl:apply-templates select="xhtml:link"/>
							</div>
						</xsl:if>
					</td>
					<td>
						<xsl:value-of select="sitemap:changefreq"/>
					</td>
					<td>
						<xsl:value-of select="sitemap:priority"/>
					</td>
					<td class="nowrap">
						<xsl:value-of select="sitemap:lastmod"/>
					</td>
				</tr>
			</xsl:for-each>
			</tbody>
		</table>
	</xsl:template>
	<xsl:template match="xhtml:link">
		<xsl:variable name="altloc">
			<xsl:value-of select="@href"/>
		</xsl:variable>
		<xsl:if test="@hreflang">
			<a href="{$altloc}" class="alternate" target="_blank">
				<xsl:value-of select="@hreflang"/>
			</a>
		</xsl:if>
		<xsl:apply-templates/>
	</xsl:template>
</xsl:stylesheet>