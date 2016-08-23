#coding=utf-8
#统计一个目录下代码的行数，包括空格、注释各自的行数
import os,re

class countCodeLines():

	#初始化
	def __init__(self):
		self.filelist = []
		self.codesTotal = 0
		self.commentTotal = 0
		self.blankTotal = 0
		self.totalList = {}
	#打开目录，返回目录下的文件列表
	def openDir(self,dir):
		
		for root,dirs,files in os.walk(dir):
			for name in files:
				if os.path.isfile(os.path.join(root,name)) and os.path.splitext(name)[1] == '.py' :
				#if os.path.isfile(name) and os.path.splitext(name)[1] == '.py' :
					self.filelist.append(os.path.join(root,name))

	#计算目录下所有文件的代码数
	def countLines(self):
		patcomment = re.compile(r"^\s*#");	#注释
		patblank = re.compile(r"^\s+$");	#空白字符串

		for file in self.filelist :
			#循环打开文件
			with open(file,'r') as f:
				for line in f.readlines():
					#计算行数
					if patblank.search(line):
						self.blankTotal+=1;
					elif patcomment.search(line):
						self.commentTotal+=1
					else:
						self.codesTotal+=1
		totalList = {'codesTotal':self.codesTotal,'commentTotal':self.commentTotal,'blankTotal':self.blankTotal}
		return totalList

if __name__ == "__main__":

	countCode = countCodeLines()
	#countCode = countCodeLines('D:\usr\python\www/demo/flask')
	countCode.openDir('D:\usr\python\www\demo/test')
	totalList = countCode.countLines()
	print totalList
