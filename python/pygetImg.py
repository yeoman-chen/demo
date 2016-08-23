#coding=utf-8

import urllib,re,string

def getHtml(url):
	page = urllib.urlopen(url)
	html = page.read()
	return html
def getImg(html):
	reg = r'class="BDE_Image" src="(.+?\.jpg)" pic_ext'
	imgre = re.compile(reg)
	imgList = re.findall(imgre,html)
	#return imgList
	x = 0
	for imgurl in imgList:
		urllib.urlretrieve(imgurl,'D:\usr\python\www\demo/test/image/%s.jpg' %x)
		x+=1

html = getHtml("http://tieba.baidu.com/p/3000655140")
img = getImg(html)
print img