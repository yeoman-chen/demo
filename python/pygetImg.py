#coding=utf-8
#简单的爬虫程序，获取指定网页的图片

import urllib,re,string

#读取网页内容
def getHtml(url):
	page = urllib.urlopen(url) #打开一个url地址
	html = page.read() #读取URL上的数据
	return html
#获取网页内容中的图片
def getImg(html):
	reg = r'class="BDE_Image" src="(.+?\.jpg)" pic_ext'
	imgre = re.compile(reg)  #把正则表达式编译成一个正则表达式对象
	imgList = re.findall(imgre,html) #读取html中包含imgre的数据
	#return imgList
	x = 0
	for imgurl in imgList:
		urllib.urlretrieve(imgurl,'D:\usr\python\www\demo/test/image/%s.jpg' %x) #保存图片到本地目录
		x+=1

html = getHtml("http://tieba.baidu.com/p/3000655140")
img = getImg(html)
print img