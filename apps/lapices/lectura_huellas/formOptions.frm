VERSION 5.00
Object = "{F9043C88-F6F2-101A-A3C9-08002B2F49FB}#1.2#0"; "comdlg32.ocx"
Begin VB.Form formOptions 
   BorderStyle     =   3  'Fixed Dialog
   Caption         =   "Options"
   ClientHeight    =   5895
   ClientLeft      =   45
   ClientTop       =   435
   ClientWidth     =   5625
   Icon            =   "formOptions.frx":0000
   LinkTopic       =   "Form1"
   MaxButton       =   0   'False
   MinButton       =   0   'False
   ScaleHeight     =   5895
   ScaleWidth      =   5625
   ShowInTaskbar   =   0   'False
   StartUpPosition =   3  'Windows Default
   Begin MSComDlg.CommonDialog CommonDialog1 
      Left            =   4320
      Top             =   5400
      _ExtentX        =   847
      _ExtentY        =   847
      _Version        =   393216
   End
   Begin VB.Frame Frame5 
      Caption         =   "Segments Colors"
      Height          =   1215
      Left            =   120
      TabIndex        =   24
      Top             =   4080
      Width           =   5415
      Begin VB.PictureBox pbDirectionsMatchColor 
         Appearance      =   0  'Flat
         BackColor       =   &H00800080&
         ForeColor       =   &H80000008&
         Height          =   255
         Left            =   960
         ScaleHeight     =   225
         ScaleWidth      =   1065
         TabIndex        =   35
         Top             =   720
         Width           =   1095
      End
      Begin VB.PictureBox pbDirectionsColor 
         Appearance      =   0  'Flat
         BackColor       =   &H000000FF&
         ForeColor       =   &H80000008&
         Height          =   255
         Left            =   960
         ScaleHeight     =   225
         ScaleWidth      =   1065
         TabIndex        =   34
         Top             =   360
         Width           =   1095
      End
      Begin VB.CheckBox cbShowDirections 
         Caption         =   "Show"
         Height          =   255
         Left            =   2160
         TabIndex        =   26
         Top             =   360
         Value           =   1  'Checked
         Width           =   855
      End
      Begin VB.CheckBox cbShowDirectionsMatched 
         Caption         =   "Show"
         Height          =   255
         Left            =   2160
         TabIndex        =   25
         Top             =   720
         Value           =   1  'Checked
         Width           =   855
      End
      Begin VB.Label Label13 
         AutoSize        =   -1  'True
         Caption         =   "Regular:"
         Height          =   195
         Left            =   240
         TabIndex        =   29
         Top             =   360
         Width           =   600
      End
      Begin VB.Label Label12 
         AutoSize        =   -1  'True
         Caption         =   "Match:"
         Height          =   195
         Left            =   240
         TabIndex        =   28
         Top             =   720
         Width           =   495
      End
      Begin VB.Label Label11 
         Caption         =   "Double click the color to change it."
         Height          =   615
         Left            =   3360
         TabIndex        =   27
         Top             =   480
         Width           =   1935
      End
   End
   Begin VB.Frame Frame4 
      Caption         =   "Segments Colors"
      Height          =   1215
      Left            =   120
      TabIndex        =   18
      Top             =   2760
      Width           =   5415
      Begin VB.PictureBox pbSegmentsMatchColor 
         Appearance      =   0  'Flat
         BackColor       =   &H00800080&
         ForeColor       =   &H80000008&
         Height          =   255
         Left            =   960
         ScaleHeight     =   225
         ScaleWidth      =   1065
         TabIndex        =   33
         Top             =   720
         Width           =   1095
      End
      Begin VB.PictureBox pbSegmentsColor 
         Appearance      =   0  'Flat
         BackColor       =   &H0000FF00&
         ForeColor       =   &H80000008&
         Height          =   255
         Left            =   960
         ScaleHeight     =   225
         ScaleWidth      =   1065
         TabIndex        =   32
         Top             =   360
         Width           =   1095
      End
      Begin VB.CheckBox cbShowSegments 
         Caption         =   "Show"
         Height          =   255
         Left            =   2160
         TabIndex        =   20
         Top             =   360
         Value           =   1  'Checked
         Width           =   855
      End
      Begin VB.CheckBox cbShowSegmentsMatched 
         Caption         =   "Show"
         Height          =   255
         Left            =   2160
         TabIndex        =   19
         Top             =   720
         Value           =   1  'Checked
         Width           =   855
      End
      Begin VB.Label Label10 
         AutoSize        =   -1  'True
         Caption         =   "Regular:"
         Height          =   195
         Left            =   240
         TabIndex        =   23
         Top             =   360
         Width           =   600
      End
      Begin VB.Label Label9 
         AutoSize        =   -1  'True
         Caption         =   "Match:"
         Height          =   195
         Left            =   240
         TabIndex        =   22
         Top             =   720
         Width           =   495
      End
      Begin VB.Label Label8 
         Caption         =   "Double click the color to change it."
         Height          =   615
         Left            =   3360
         TabIndex        =   21
         Top             =   480
         Width           =   1935
      End
   End
   Begin VB.Frame Frame3 
      Caption         =   "Minutiae Colors"
      Height          =   1215
      Left            =   120
      TabIndex        =   12
      Top             =   1440
      Width           =   5415
      Begin VB.PictureBox pbMinutiaeMatchColor 
         Appearance      =   0  'Flat
         BackColor       =   &H00800080&
         ForeColor       =   &H80000008&
         Height          =   255
         Left            =   960
         ScaleHeight     =   225
         ScaleWidth      =   1065
         TabIndex        =   31
         Top             =   720
         Width           =   1095
      End
      Begin VB.PictureBox pbMinutiaeColor 
         Appearance      =   0  'Flat
         BackColor       =   &H00FF0000&
         ForeColor       =   &H80000008&
         Height          =   255
         Left            =   960
         ScaleHeight     =   225
         ScaleWidth      =   1065
         TabIndex        =   30
         Top             =   360
         Width           =   1095
      End
      Begin VB.CheckBox cbShowMinutiaeMatched 
         Caption         =   "Show"
         Height          =   255
         Left            =   2160
         TabIndex        =   16
         Top             =   720
         Value           =   1  'Checked
         Width           =   855
      End
      Begin VB.CheckBox cbShowMinutiae 
         Caption         =   "Show"
         Height          =   255
         Left            =   2160
         TabIndex        =   15
         Top             =   360
         Value           =   1  'Checked
         Width           =   855
      End
      Begin VB.Label Label7 
         Caption         =   "Double click the color to change it."
         Height          =   615
         Left            =   3360
         TabIndex        =   17
         Top             =   480
         Width           =   1935
      End
      Begin VB.Label Label6 
         AutoSize        =   -1  'True
         Caption         =   "Match:"
         Height          =   195
         Left            =   240
         TabIndex        =   14
         Top             =   720
         Width           =   495
      End
      Begin VB.Label Label5 
         AutoSize        =   -1  'True
         Caption         =   "Regular:"
         Height          =   195
         Left            =   240
         TabIndex        =   13
         Top             =   360
         Width           =   600
      End
   End
   Begin VB.Frame Frame2 
      Caption         =   "Verify"
      Height          =   1215
      Left            =   2880
      TabIndex        =   7
      Top             =   120
      Width           =   2655
      Begin VB.TextBox txtVerifThres 
         Alignment       =   1  'Right Justify
         BeginProperty DataFormat 
            Type            =   0
            Format          =   "0"
            HaveTrueFalseNull=   0
            FirstDayOfWeek  =   0
            FirstWeekOfYear =   0
            LCID            =   1046
            SubFormatType   =   0
         EndProperty
         Height          =   285
         IMEMode         =   3  'DISABLE
         Left            =   1680
         MaxLength       =   3
         TabIndex        =   4
         Top             =   360
         Width           =   855
      End
      Begin VB.TextBox txtVerifRotTol 
         Alignment       =   1  'Right Justify
         BeginProperty DataFormat 
            Type            =   0
            Format          =   "0"
            HaveTrueFalseNull=   0
            FirstDayOfWeek  =   0
            FirstWeekOfYear =   0
            LCID            =   1046
            SubFormatType   =   0
         EndProperty
         Height          =   285
         Left            =   1680
         MaxLength       =   3
         TabIndex        =   6
         Top             =   720
         Width           =   855
      End
      Begin VB.Label Label2 
         AutoSize        =   -1  'True
         Caption         =   "Thresold:"
         Height          =   195
         Left            =   960
         TabIndex        =   10
         Top             =   390
         Width           =   660
      End
      Begin VB.Label Label1 
         AutoSize        =   -1  'True
         Caption         =   "Rotation tolerance:"
         Height          =   195
         Left            =   240
         TabIndex        =   8
         Top             =   750
         Width           =   1350
      End
   End
   Begin VB.Frame Frame1 
      Caption         =   "Identify"
      Height          =   1215
      Left            =   120
      TabIndex        =   1
      Top             =   120
      Width           =   2655
      Begin VB.TextBox txtIdentRotTol 
         Alignment       =   1  'Right Justify
         BeginProperty DataFormat 
            Type            =   0
            Format          =   "0"
            HaveTrueFalseNull=   0
            FirstDayOfWeek  =   0
            FirstWeekOfYear =   0
            LCID            =   1046
            SubFormatType   =   0
         EndProperty
         Height          =   285
         Left            =   1680
         MaxLength       =   3
         TabIndex        =   2
         Top             =   720
         Width           =   855
      End
      Begin VB.TextBox txtIdentThres 
         Alignment       =   1  'Right Justify
         BeginProperty DataFormat 
            Type            =   0
            Format          =   "0"
            HaveTrueFalseNull=   0
            FirstDayOfWeek  =   0
            FirstWeekOfYear =   0
            LCID            =   1046
            SubFormatType   =   0
         EndProperty
         Height          =   285
         IMEMode         =   3  'DISABLE
         Left            =   1680
         MaxLength       =   3
         TabIndex        =   0
         Top             =   360
         Width           =   855
      End
      Begin VB.Label Label4 
         AutoSize        =   -1  'True
         Caption         =   "Rotation tolerance:"
         Height          =   195
         Left            =   240
         TabIndex        =   5
         Top             =   750
         Width           =   1350
      End
      Begin VB.Label Label3 
         AutoSize        =   -1  'True
         Caption         =   "Thresold:"
         Height          =   195
         Left            =   960
         TabIndex        =   3
         Top             =   390
         Width           =   660
      End
   End
   Begin VB.CommandButton btOk 
      Caption         =   "OK"
      Default         =   -1  'True
      Height          =   375
      Left            =   1725
      TabIndex        =   9
      Top             =   5400
      Width           =   975
   End
   Begin VB.CommandButton btCancel 
      Cancel          =   -1  'True
      Caption         =   "Cancel"
      Height          =   375
      Left            =   2925
      TabIndex        =   11
      Top             =   5400
      Width           =   975
   End
End
Attribute VB_Name = "formOptions"
Attribute VB_GlobalNameSpace = False
Attribute VB_Creatable = False
Attribute VB_PredeclaredId = True
Attribute VB_Exposed = False
'-------------------------------------------------------------------------------
'GrFinger Sample
'(c) 2005 Griaule Tecnologia Ltda.
'http://www.griaule.com
'-------------------------------------------------------------------------------
'
'This sample is provided with "GrFinger Fingerprint Recognition Library" and
'can't run without it. It's provided just as an example of using GrFinger
'Fingerprint Recognition Library and should not be used as basis for any
'commercial product.
'
'Griaule Tecnologia makes no representations concerning either the merchantability
'of this software or the suitability of this sample for any particular purpose.
'
'THIS SAMPLE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR
'IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES
'OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
'IN NO EVENT SHALL GRIAULE BE LIABLE FOR ANY DIRECT, INDIRECT,
'INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
'NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
'DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
'THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
'(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
'THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
'
'You can download the free version of GrFinger directly from Griaule website.
'
'These notices must be retained in any copies of any part of this
'documentation and/or sample.
'
'-------------------------------------------------------------------------------

' -----------------------------------------------------------------------------------
' GUI routines: "Options" form
' -----------------------------------------------------------------------------------

Option Explicit

Private clMinutiaeColor As Long
Private clMinutiaeMatchColor As Long
Private clSegmentsColor As Long
Private clSegmentsMatchColor As Long
Private clDirectionsColor As Long
Private clDirectionsMatchColor As Long
Private bShowMinutiae As Integer
Private bShowMinutiaeMatch As Integer
Private bShowSegments As Integer
Private bShowSegmentsMatch As Integer
Private bShowDirections As Integer
Private bShowDirectionsMatch As Integer
Private initialized As Boolean

Private bOkClicked As Boolean

' Commit changes made by user
Public Sub AcceptChanges()
    clMinutiaeColor = pbMinutiaeColor.BackColor
    clMinutiaeMatchColor = pbMinutiaeMatchColor.BackColor
    clSegmentsColor = pbSegmentsColor.BackColor
    clSegmentsMatchColor = pbSegmentsMatchColor.BackColor
    clDirectionsColor = pbDirectionsColor.BackColor
    clDirectionsMatchColor = pbDirectionsMatchColor.BackColor
    bShowMinutiae = cbShowMinutiae.Value
    bShowMinutiaeMatch = cbShowMinutiaeMatched.Value
    bShowSegments = cbShowSegments.Value
    bShowSegmentsMatch = cbShowSegmentsMatched.Value
    bShowDirections = cbShowDirections.Value
    bShowDirectionsMatch = cbShowDirectionsMatched.Value
End Sub

' Set current values of threshold and rotation for verification and identification
Public Sub setParameters(ByVal thresholdId As Integer, ByVal rotationMaxId As Integer, ByVal thresholdVr As Integer, ByVal rotationMaxVr As Integer)
    txtIdentThres.Text = Trim(str(thresholdId))
    txtIdentRotTol.Text = Trim(str(rotationMaxId))
    txtVerifThres.Text = Trim(str(thresholdVr))
    txtVerifRotTol.Text = Trim(str(rotationMaxVr))
End Sub

' Show dialog and get new values set by user
Public Function getParameters(ByRef thresholdId As Long, ByRef rotationMaxId As Long, ByRef thresholdVr As Long, ByRef rotationMaxVr As Long, _
        ByRef minutiaeColor As Long, ByRef minutiaeMatchColor As Long, ByRef segmentsColor As Long, ByRef segmentsMatchColor As Long, _
        ByRef directionsColor As Long, ByRef directionsMatchColor As Long)
    Me.Show vbModal
    If Not (bOkClicked) Then
        getParameters = False
        Exit Function
    End If
    ' convert threshold and rotation values
    thresholdId = Val(txtIdentThres.Text)
    rotationMaxId = Val(txtIdentRotTol.Text)
    thresholdVr = Val(txtVerifThres.Text)
    rotationMaxVr = Val(txtVerifRotTol.Text)
    ' get colors
    minutiaeColor = pbMinutiaeColor.BackColor
    minutiaeMatchColor = pbMinutiaeMatchColor.BackColor
    segmentsColor = pbSegmentsColor.BackColor
    segmentsMatchColor = pbSegmentsMatchColor.BackColor
    directionsColor = pbDirectionsColor.BackColor
    directionsMatchColor = pbDirectionsMatchColor.BackColor
    ' check if anything should not be displayed
    If cbShowMinutiae.Value = 0 Then minutiaeColor = GR_IMAGE_NO_COLOR
    If cbShowMinutiaeMatched.Value = 0 Then minutiaeMatchColor = GR_IMAGE_NO_COLOR
    If cbShowSegments.Value = 0 Then segmentsColor = GR_IMAGE_NO_COLOR
    If cbShowSegmentsMatched.Value = 0 Then segmentsMatchColor = GR_IMAGE_NO_COLOR
    If cbShowDirections.Value = 0 Then directionsColor = GR_IMAGE_NO_COLOR
    If cbShowDirectionsMatched.Value = 0 Then directionsMatchColor = GR_IMAGE_NO_COLOR
    getParameters = True
End Function

' Flag that user pressed the "Cancel" button and close dialog
Private Sub btCancel_Click()
 bOkClicked = False
 Me.Hide
End Sub

' Flag that user pressed the "OK" button and close dialog
Private Sub btOk_Click()
 bOkClicked = True
 Me.Hide
End Sub

' Set current values in GUI
Private Sub Form_Load()
    ' if not initialized, get initial values
    If Not (initialized) Then Call AcceptChanges
    ' set current values in GUI
    pbMinutiaeColor.BackColor = clMinutiaeColor
    pbMinutiaeMatchColor.BackColor = clMinutiaeMatchColor
    pbSegmentsColor.BackColor = clSegmentsColor
    pbSegmentsMatchColor.BackColor = clSegmentsMatchColor
    pbDirectionsColor.BackColor = clDirectionsColor
    pbDirectionsMatchColor.BackColor = clDirectionsMatchColor
    cbShowMinutiae.Value = bShowMinutiae
    cbShowMinutiaeMatched.Value = bShowMinutiaeMatch
    cbShowSegments.Value = bShowSegments
    cbShowSegmentsMatched.Value = bShowSegmentsMatch
    cbShowDirections.Value = bShowDirections
    cbShowDirectionsMatched.Value = bShowDirectionsMatch
    ' flag as already initialized
    initialized = True
End Sub

' display color dialog and set minutiae color
Private Sub pbMinutiaeColor_DblClick()
    CommonDialog1.Color = pbMinutiaeColor.BackColor
    CommonDialog1.ShowColor
    If Not (CommonDialog1.CancelError) Then
        pbMinutiaeColor.BackColor = CommonDialog1.Color
    End If
End Sub

' display color dialog and set matching minutiae color
Private Sub pbMinutiaeMatchColor_DblClick()
    CommonDialog1.Color = pbMinutiaeMatchColor.BackColor
    CommonDialog1.ShowColor
    If Not (CommonDialog1.CancelError) Then
        pbMinutiaeMatchColor.BackColor = CommonDialog1.Color
    End If
End Sub

' display color dialog and set segments color
Private Sub pbSegmentsColor_DblClick()
    CommonDialog1.Color = pbSegmentsColor.BackColor
    CommonDialog1.ShowColor
    If Not (CommonDialog1.CancelError) Then
        pbSegmentsColor.BackColor = CommonDialog1.Color
    End If
End Sub

' display color dialog and set matching segments color
Private Sub pbSegmentsMatchColor_DblClick()
    CommonDialog1.Color = pbSegmentsMatchColor.BackColor
    CommonDialog1.ShowColor
    If Not (CommonDialog1.CancelError) Then
        pbSegmentsMatchColor.BackColor = CommonDialog1.Color
    End If
End Sub

' display color dialog and set directions color
Private Sub pbDirectionsColor_DblClick()
    CommonDialog1.Color = pbDirectionsColor.BackColor
    CommonDialog1.ShowColor
    If Not (CommonDialog1.CancelError) Then
        pbDirectionsColor.BackColor = CommonDialog1.Color
    End If
End Sub

' display color dialog and set matching directions color
Private Sub pbDirectionsMatchColor_DblClick()
    CommonDialog1.Color = pbDirectionsMatchColor.BackColor
    CommonDialog1.ShowColor
    If Not (CommonDialog1.CancelError) Then
        pbDirectionsMatchColor.BackColor = CommonDialog1.Color
    End If
End Sub

