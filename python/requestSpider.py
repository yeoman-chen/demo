#coding=utf-8

import requests
import csv
import random
import time
import urllib
from bs4 import BeautifulSoup
import sys
reload(sys)
sys.setdefaultencoding('utf8')

class requestSpider() :

	def __init__(self):
		self.headers = {
			'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
	        'Accept-Encoding':  'gzip, deflate, sdch',
	        'Accept-Language':  'zh-CN,zh;q=0.8',
	        'Cache-Control':  'max-age=0',
	        'Connection':  'keep-alive',
	        'User-Agent':  'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.235'
		}
		self.cookie = ''


	#模拟登陆知乎
	def login(self):
		
		session = requests.session()
		res = session.get('http://www.zhihu.com',headers = self.headers).content
		_xsrf = BeautifulSoup(res,"html.parser").find("input",attrs={"name":"_xsrf"})['value']

		login_data = {
			'_xsrf':_xsrf,
			'password' : '19870924',
			'remember_me' : 'true',
			'email' : 'fast042@163.com'
		}
		#模拟登陆
		res1 = session.post('https://www.zhihu.com/login/email',data = login_data,headers = self.headers)
		self.cookie = res1.cookies
		#print res1.cookies
		#使用cookie持续登陆
		#res2 = session.get('http://www.zhihu.com',cookies=res1.cookies,headers = self.headers)
		#print res2.text

	#获取知乎的内容
	def get_zhihu_content(self,url):
		timeout = random.choice(range(80,180))
		session = requests.session()
		res = session.get('http://www.zhihu.com',headers = self.headers).content
		_xsrf = BeautifulSoup(res,"html.parser").find("input",attrs={"name":"_xsrf"})['value']
		res = session.get('http://www.zhihu.com/question/19605013',cookies =  self.cookie,headers = self.headers)
		return res.text
		
	#获取知乎的内容
	def get_zhihu_data(self,html):
		final = []
		bs = BeautifulSoup(html,"html.parser")
		body = bs.body
		data = body.find("div",{'id' : 'zh-question-answer-wrap'})
		divList = data.find_all('div', attrs = {'tabindex' : '-1'})
		for div in divList:
			temp = []
			#获取投票信息
			votebar = div.find("div",attrs = {'class' : 'zm-votebar'})
			zanTotal = votebar.find('button',attrs = {'class' : 'up '}).find('span',attrs = {'class' : 'count'})
			if zanTotal is None :
				zanTotal = 0;
			else:
				zanTotal = zanTotal.string
			temp.append(zanTotal)
			fdTotal = votebar.find('button',attrs = {'class' : 'down '}).find('span',attrs = {'class' : 'count'})
			if fdTotal is None :
				fdTotal = 0;
			else:
				fdTotal = fdTotal.string
			temp.append(fdTotal)
			#获取用户信息
			userDiv = div.find("div",attrs = {'class' : 'zm-item-answer-author-info'})
			avatar = userDiv.find("img",attrs = {'class' : 'zm-list-avatar avatar'})
			userSpan = userDiv.find("span",attrs = {'class' : 'author-link-line'})
			if userSpan is None :
				userName = ''
				userZhiYe = ''
			else:
				userName = userSpan.find("a",attrs = {'class' : 'author-link'})
				if userName is None :
					userName = ''
				else:
					userName = userName.string
				userZhiYe = userSpan.find("span",attrs = {'class' : 'bio'})
				if userZhiYe is None :
					userZhiYe = ''
				else:
					userZhiYe = userZhiYe.string
			temp.append(userName)
			temp.append(userZhiYe)
			content = div.find("div",attrs = {'class' : 'zm-editable-content clearfix'})
			if content is None :
				content = ''
			else :
				content = content.string
			temp.append(content)
			pusDate = div.find("a",attrs = {'class' : 'answer-date-link meta-item'})
			if pusDate is None :
				pusDate = ''
			else :
				pusDate = pusDate.string
			temp.append(pusDate)

			comment = div.find("a",attrs = {'class' : 'meta-item toggle-comment js-toggleCommentBox'})
			if comment is None :
				comment = ''
			else :
				comment = comment.string
			temp.append(comment)
			final.append(temp)
			#print temp
			#break
		return final
		#print data

	#获取html内容
	def get_content(self,url):

		timeout = random.choice(range(80,180))

		while True:
				rep = requests.get(url,headers = self.headers,timeout = timeout)
				rep.encoding = 'utf-8'
				break

		return rep.text

	#获取网页内容
	def get_data(self,html):
		final = []
		bs = BeautifulSoup(html,"html.parser") #创建BeautifulSoup对象
		body = bs.body
		data = body.find('div',{'id':'7d'}) #查找
		ul = data.find('ul')  # 获取ul部分
		li = ul.find_all('li') # 获取所有的li

		for day in li : # 对每个li标签中的内容进行遍历
			temp = []
			date = day.find("h1").string 
			temp.append(date)
			inf = day.find_all("p")
			temp.append(inf[0].string)
			if inf[1].find('span') is None :
				temperature_highest = None # 天气预报可能没有当天的最高气温（到了傍晚，就是这样），需要加个判断语句,来输出最低气温
			else:
				temperature_highest = inf[1].find('span').string # 找到最高温
				temperature_highest = temperature_highest.decode('utf-8').replace('℃', '')  # 到了晚上网站会变，最高温度后面也有个℃
			temperature_lowest = inf[1].find('i').string # 找到最高温
			temperature_lowest = temperature_lowest.decode('utf-8').replace('℃', '')  # 到了晚上网站会变，最高温
			temp.append(temperature_highest)
			temp.append(temperature_lowest)
			final.append(temp)

		return final

	#把数据写到excel
	def save_data_excel(self,data,file_name):
		 
		 with open(file_name,'a') as f :
		 	f_csv = csv.writer(f)
		 	f_csv.writerows(data)
	
	#保存图片到本地
	def save_img_files(self,imgUrl,fileName):
		u = urllib.urlopen(imgUrl)
		data = u.read()
		f = open(fileName,'wb')
		f.write(data)
		print u"正在悄悄的保存图片为",fileName
		f.close()


if __name__ == '__main__' :

	reqSpider = requestSpider()
	#爬取天气预报
	#html = reqSpider.get_content('http://www.weather.com.cn/weather/101190401.shtml')
	#res = reqSpider.get_data(html)
	#reqSpider.save_data_excel(res,'weather.csv')
	#print res
	#reqSpider.save_img_files('http://i.weather.com.cn/images/cn/trip/2016/05/06/06102613094FFA1FF1EB28740A1F9CD53B687D6A_s.jpg','image/dddd.jpg')
	#爬取知乎网页
	reqSpider.login()
	html = reqSpider.get_zhihu_content('http://www.zhihu.com/question/19605013')
	data = reqSpider.get_zhihu_data(html)


	